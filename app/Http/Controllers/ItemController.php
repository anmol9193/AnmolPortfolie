<?php

namespace App\Http\Controllers;

use App\Models\PortfolioItem;
use App\Support\Content;
use App\Support\Uploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * One CRUD for every content collection (projects, experiences, education, ...).
 * The fields of each collection come from config/content.php.
 */
class ItemController extends Controller
{
    public function index(string $type): View
    {
        return view('admin.items.index', [
            'type' => $type,
            'collection' => $this->collection($type),
            'items' => PortfolioItem::where('type', $type)->orderBy('position')->orderBy('id')->get(),
        ]);
    }

    public function create(string $type): View
    {
        return view('admin.items.form', [
            'type' => $type,
            'collection' => $this->collection($type),
            'item' => new PortfolioItem(['type' => $type, 'data' => [], 'position' => (int) PortfolioItem::where('type', $type)->max('position') + 1]),
        ]);
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $collection = $this->collection($type);
        $item = new PortfolioItem(['type' => $type, 'data' => []]);

        $this->fill($request, $item, $collection);

        return redirect()->route('admin.items.index', $type)->with('status', ucfirst($collection['singular']).' added.');
    }

    public function edit(string $type, PortfolioItem $item): View
    {
        abort_unless($item->type === $type, 404);

        return view('admin.items.form', [
            'type' => $type,
            'collection' => $this->collection($type),
            'item' => $item,
        ]);
    }

    public function update(Request $request, string $type, PortfolioItem $item): RedirectResponse
    {
        abort_unless($item->type === $type, 404);

        $collection = $this->collection($type);

        $this->fill($request, $item, $collection);

        return redirect()->route('admin.items.index', $type)->with('status', ucfirst($collection['singular']).' updated.');
    }

    public function destroy(string $type, PortfolioItem $item): RedirectResponse
    {
        abort_unless($item->type === $type, 404);

        $collection = $this->collection($type);

        foreach ($collection['fields'] as $key => $field) {
            if ($field[1] === 'image') {
                Uploads::delete($item->field($key));
            }
        }

        $item->delete();
        Content::forget();

        return back()->with('status', ucfirst($collection['singular']).' deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function collection(string $type): array
    {
        $collection = config("content.collections.$type");

        abort_unless($collection, 404);

        return $collection;
    }

    /**
     * Validate the request against the collection's fields and save the item.
     *
     * @param  array<string, mixed>  $collection
     */
    protected function fill(Request $request, PortfolioItem $item, array $collection): void
    {
        $rules = ['position' => ['nullable', 'integer', 'min:0', 'max:9999']];
        $labels = ['position' => 'Order'];

        foreach ($collection['fields'] as $key => $field) {
            $required = ($field['required'] ?? false) ? 'required' : 'nullable';
            $labels[$key] = $field[0];

            $rules[$key] = match ($field[1]) {
                'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
                'checkbox' => ['nullable', 'boolean'],
                'url' => [$required, 'url', 'max:255'],
                'color' => [$required, 'regex:/^#[0-9a-fA-F]{6}$/'],
                'select' => [$required, Rule::in(array_keys($field['options']))],
                'icon' => [$required, Rule::in(array_keys(config('icons')))],
                'textarea' => [$required, 'string', 'max:2000'],
                default => [$required, 'string', 'max:255'],
            };
        }

        $input = $request->validate($rules, [], $labels);
        $data = $item->data ?? [];

        foreach ($collection['fields'] as $key => $field) {
            if ($field[1] === 'image') {
                if ($request->hasFile($key)) {
                    Uploads::delete($data[$key] ?? null);
                    $data[$key] = Uploads::store($request->file($key), $item->type);
                } elseif ($request->boolean("remove_$key")) {
                    Uploads::delete($data[$key] ?? null);
                    $data[$key] = '';
                }

                continue;
            }

            $data[$key] = $field[1] === 'checkbox' ? $request->boolean($key) : (string) ($input[$key] ?? '');
        }

        $item->data = $data;
        $item->position = (int) ($input['position'] ?? $item->position ?? 0);
        $item->save();

        Content::forget();
    }
}
