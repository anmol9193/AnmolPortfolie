@php
    $go = '<svg class="go" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
    $phone = content('contact.phone');
@endphp
<!-- CONTACT -->
<section id="contact">
  <div class="container">
    @include('site.sections._head', ['group' => 'contact'])
    <div class="contact-grid">
      <div class="c-list">
        @if (content('contact.email'))
          <a href="mailto:{{ content('contact.email') }}" class="card c-item reveal">
            <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="M22 6l-10 7L2 6"/></svg></div>
            <div><small>{{ content('labels.email_title') }}</small><b>{{ content('contact.email') }}</b></div>
            {!! $go !!}
          </a>
        @endif
        @if ($phone)
          <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="card c-item reveal">
            <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg></div>
            <div><small>{{ content('labels.phone_title') }}</small><b>{{ $phone }}</b></div>
            {!! $go !!}
          </a>
        @endif
        @if (content('contact.whatsapp'))
          <a href="https://wa.me/{{ content('contact.whatsapp') }}" target="_blank" rel="noopener" class="card c-item reveal">
            <div class="ic"><svg viewBox="0 0 24 24" fill="currentColor">@include('site.icons.whatsapp')</svg></div>
            <div><small>{{ content('labels.whatsapp_title') }}</small><b>{{ content('labels.whatsapp_text') }}</b></div>
            {!! $go !!}
          </a>
        @endif
        @if (content('contact.linkedin'))
          <a href="{{ content('contact.linkedin') }}" target="_blank" rel="noopener" class="card c-item reveal">
            <div class="ic"><svg viewBox="0 0 24 24" fill="currentColor">@include('site.icons.linkedin')</svg></div>
            <div><small>{{ content('labels.linkedin_title') }}</small><b>{{ content('contact.linkedin_label') }}</b></div>
            {!! $go !!}
          </a>
        @endif
        @if (content('contact.instagram'))
          <a href="{{ content('contact.instagram') }}" target="_blank" rel="noopener" class="card c-item reveal">
            <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">@include('site.icons.instagram')</svg></div>
            <div><small>{{ content('labels.instagram_title') }}</small><b>{{ content('contact.instagram_label') }}</b></div>
            {!! $go !!}
          </a>
        @endif
        @if (content('contact.address'))
          <div class="card c-item reveal">
            <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
            <div><small>{{ content('labels.address_title') }}</small><b>{{ content('contact.address') }}</b></div>
          </div>
        @endif
      </div>
      <form class="card reveal" id="contactForm" method="POST" action="{{ route('contact.send') }}">
        @csrf
        <h3>{{ content('contact.form_title') }}</h3>
        <p>{{ content('contact.form_text') }}</p>

        @if (session('contact_sent'))
          <div class="form-note ok" role="status">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
            <span>{{ content('contact.form_success') }}</span>
          </div>
        @endif
        @if ($errors->contact->any())
          <div class="form-note bad" role="alert">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
            <span>{{ $errors->contact->first() }}</span>
          </div>
        @endif


        <div class="f-row">
          <div class="field"><input id="fName" name="name" value="{{ old('name') }}" required maxlength="80" autocomplete="name" placeholder=" "><label for="fName">{{ content('labels.form_name') }}</label></div>
          <div class="field"><input id="fEmail" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" placeholder=" "><label for="fEmail">{{ content('labels.form_email') }}</label></div>
        </div>
        <div class="f-row">
          <div class="field"><input id="fPhone" name="phone" type="tel" value="{{ old('phone') }}" required maxlength="30" autocomplete="tel" placeholder=" "><label for="fPhone">{{ content('labels.form_phone') }}</label></div>
          <div class="field"><input id="fSubject" name="subject" value="{{ old('subject') }}" required maxlength="150" placeholder=" "><label for="fSubject">{{ content('labels.form_subject') }}</label></div>
        </div>
        <div class="field"><textarea id="fMsg" name="message" rows="5" required maxlength="3000" placeholder=" ">{{ old('message') }}</textarea><label for="fMsg">{{ content('labels.form_message') }}</label></div>
        <button class="btn btn-primary" id="contactBtn" type="submit"><span>{{ content('labels.form_button') }}</span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button>
      </form>
    </div>
  </div>
</section>
