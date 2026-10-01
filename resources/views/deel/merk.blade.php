{{-- Woordmerk Shop & Eat, zoals in shop-and-eat.html: een schijf met een stip
     erin naast de naam. Op een donker vlak roep je 'm aan met ['licht' => true],
     dan draait het rondje om. Stijl staat in assets/stijl/dashboard.css. --}}
<span class="merk {{ ($licht ?? false) ? 'licht' : '' }} {{ ($klein ?? false) ? 'klein' : '' }}">
    <i aria-hidden="true"></i>Shop &amp; Eat
</span>
