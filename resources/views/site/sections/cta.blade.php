<!-- CTA -->
<section class="cta">
  <div class="container">
    <div class="cta-box reveal">
      <small>{{ content('cta.label') }}</small>
      <h2>{{ rich(content('cta.title')) }}</h2>
      <a href="{{ $onHome ? '#contact' : url('contact') }}" class="btn magnetic">{{ content('cta.button') }}
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
    </div>
  </div>
</section>
