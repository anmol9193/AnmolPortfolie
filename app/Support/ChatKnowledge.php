<?php

namespace App\Support;

class ChatKnowledge
{
    /** @var array<string, string>|null */
    private ?array $loadedRecords = null;

    /** @return array<string, string> */
    public function records(): array
    {
        if ($this->loadedRecords !== null) {
            return $this->loadedRecords;
        }
        Content::forget();
        $records = [];
        $fields = [
            'profile' => ['site.name', 'site.tagline', 'hero.sub', 'hero.status', 'about.intro', 'about.text_1', 'about.text_2', 'about.location', 'about.location_text'],
            'contact' => ['contact.email', 'contact.phone', 'contact.whatsapp', 'contact.linkedin', 'contact.instagram', 'contact.address'],
        ];
        foreach ($fields as $topic => $keys) {
            foreach ($keys as $key) {
                $value = trim(strip_tags(Content::get($key)));
                if ($value !== '') {
                    $records[$key] = $topic.' '.$key.': '.$value;
                }
            }
        }
        foreach (['projects', 'skills', 'experiences', 'education', 'services', 'process', 'countries'] as $type) {
            foreach (Content::items($type) as $item) {
                $values = [];
                foreach (config("content.collections.$type.fields", []) as $field => $definition) {
                    if (in_array($definition[1], ['text', 'textarea', 'tags', 'url'], true)) {
                        $value = $item->field($field);
                        if (is_string($value) && trim($value) !== '') {
                            $values[] = $field.': '.strip_tags($value);
                        }
                    }
                }
                if ($values !== []) {
                    $records[$type.'.'.$item->id] = $type.' | '.implode(' | ', $values);
                }
            }
        }

        return $this->loadedRecords = $records;
    }

    /** @return array<string, string> */
    public function retrieve(string $question): array
    {
        $question = mb_strtolower($question);
        if (preg_match('/ignore\s+(?:all\s+)?(?:previous|prior|system)\s+instructions|reveal\s+(?:the\s+)?(?:system\s+prompt|api\s+key)/u', $question)) {
            return [];
        }
        $aliases = [
            'projects' => 'projects project work portfolio kaam pariyojana परियोजना परियोजनाओं प्रोजेक्ट काम',
            'skills' => 'skills skill stack technologies technology tools hunar कौशल तकनीक स्किल्स',
            'experiences' => 'experience experiences career job jobs company companies naukri anubhav अनुभव नौकरी',
            'education' => 'education study studies college degree qualification padhai shiksha पढ़ाई शिक्षा',
            'services' => 'services service offer offers seva सेवा सेवाएं',
            'contact' => 'contact email phone whatsapp linkedin instagram address sampark संपर्क ईमेल फोन',
            'profile' => 'profile about yourself overview name location based introduce introduction naam bare kahan नाम परिचय कहाँ',
            'process' => 'process workflow steps prakriya प्रक्रिया',
            'countries' => 'countries country international clients desh देश',
        ];
        $tokens = preg_split('/[^\p{L}\p{M}\p{N}+#.]+/u', $question, -1, PREG_SPLIT_NO_EMPTY);
        $tokens = array_map(fn (string $token) => trim($token, '.'), $tokens);
        $stop = explode(' ', 'what which are your you the a an is in of to me tell about how do does can have has my i hai hain kya ke ki ka aur mujhe batao apne aap अपने क्या है हैं के की का मुझे बताओ');
        $tokens = array_values(array_diff($tokens, $stop));
        $topics = [];
        foreach ($aliases as $topic => $words) {
            if (array_intersect($tokens, explode(' ', $words)) !== []) {
                $topics[] = $topic;
            }
        }
        $scores = [];
        $records = $this->records();
        foreach ($records as $id => $text) {
            $topic = explode('.', $id)[0];
            $topic = in_array($topic, ['site', 'hero', 'about'], true) ? 'profile' : $topic;
            $score = in_array($topic, $topics, true) ? 5 : 0;
            foreach ($tokens as $token) {
                if (mb_strlen($token) >= 3 && preg_match('/(?<![\p{L}\p{M}\p{N}])'.preg_quote($token, '/').'(?![\p{L}\p{M}\p{N}])/u', mb_strtolower($text))) {
                    $score++;
                }
            }
            if ($score > 0) {
                $scores[$id] = $score;
            }
        }
        arsort($scores);

        return array_intersect_key($records, array_flip(array_slice(array_keys($scores), 0, 12)));
    }

    /** @param list<string> $used
     * @return list<string>
     */
    public function suggestions(array $used = [], string $question = ''): array
    {
        $records = $this->records();
        $candidates = [];
        foreach (['projects' => 'What projects have you built?', 'skills' => 'What are your main skills?', 'experiences' => 'Where have you worked?', 'education' => 'What is your education?', 'services' => 'What services do you offer?', 'contact' => 'How can I contact you?', 'profile' => 'Tell me about yourself.', 'process' => 'What is your development process?', 'countries' => 'Which countries have you worked with?'] as $topic => $label) {
            if (collect($records)->contains(fn (string $text) => str_starts_with($text, $topic.' '))) {
                $candidates[] = $label;
            }
        }
        foreach ($records as $id => $text) {
            if (str_starts_with($id, 'projects.') && preg_match('/title: ([^|]+)/', $text, $match)) {
                $candidates[] = 'Tell me about '.trim($match[1]).'.';
            }
        }
        $relevant = array_keys($this->retrieve($question));
        usort($candidates, fn (string $a, string $b) => count(array_intersect(array_keys($this->retrieve($b)), $relevant)) <=> count(array_intersect(array_keys($this->retrieve($a)), $relevant)));

        $excluded = array_map(mb_strtolower(...), [...$used, $question]);

        return array_slice(array_values(array_filter(array_unique($candidates), fn (string $candidate) => ! in_array(mb_strtolower($candidate), $excluded, true))), 0, 1);
    }
}
