{{--
    Hero illustration: a teacher leaning in to explain a book to a student
    at a shared desk, a geometry board behind them and a small heart and
    spark between them for care and the "aha" moment. Flat brand colours,
    inline SVG (no image request). Decorative: the hero text says it all.
--}}
<svg {{ $attributes->merge(['viewBox' => '0 0 400 400', 'fill' => 'none', 'aria-hidden' => 'true', 'focusable' => 'false']) }} xmlns="http://www.w3.org/2000/svg">
    {{-- Board --}}
    <g>
        <rect x="168" y="34" width="168" height="98" rx="14" fill="#08282a" />
        <rect x="168" y="34" width="168" height="98" rx="14" stroke="#f6e7d4" stroke-opacity=".25" stroke-width="3" />
        <path d="M190 112 L232 58 L232 112 Z" stroke="#fff" stroke-opacity=".85" stroke-width="2.5" stroke-linejoin="round" />
        <path d="M221 112 v-11 h11" stroke="#fff" stroke-opacity=".85" stroke-width="2" />
        <path d="M200 88 a14 14 0 0 1 9 12" stroke="#ffd28f" stroke-width="2" />
        <text x="246" y="80" fill="#ffd28f" font-size="18" font-weight="700" font-family="ui-sans-serif, system-ui, sans-serif">a²+b²</text>
        <text x="252" y="106" fill="#ffffff" fill-opacity=".85" font-size="18" font-weight="700" font-family="ui-sans-serif, system-ui, sans-serif">= c²</text>
    </g>

    {{-- Spark of understanding --}}
    <path d="M352 150 l6 15 15 6 -15 6 -6 15 -6 -15 -15 -6 15 -6 z" fill="#fe704d" />
    <circle cx="334" cy="140" r="3" fill="#ffd28f" />
    <circle cx="372" cy="196" r="2.5" fill="#ffd28f" />

    {{-- Teacher --}}
    <g>
        <circle cx="125" cy="146" r="34" fill="#2a1d1a" />
        <circle cx="100" cy="124" r="15" fill="#2a1d1a" />
        <rect x="116" y="172" width="18" height="18" rx="6" fill="#c98a63" />
        <path d="M76 304 C78 232 92 192 125 186 C160 192 174 232 176 304 Z" fill="#fe704d" />
        <path d="M98 194 C120 214 150 232 172 268" stroke="#0f5e5c" stroke-width="14" stroke-linecap="round" />
        <circle cx="125" cy="150" r="28" fill="#e3a77f" />
        <path d="M97 148 C98 122 118 112 140 117 C154 121 158 134 155 144 C141 131 118 129 97 148 Z" fill="#2a1d1a" />
        <path d="M112 153 q5 -5 10 0" stroke="#2a1d1a" stroke-width="2.6" stroke-linecap="round" />
        <path d="M130 153 q5 -5 10 0" stroke="#2a1d1a" stroke-width="2.6" stroke-linecap="round" />
        <circle cx="112" cy="163" r="5" fill="#fe704d" fill-opacity=".35" />
        <circle cx="142" cy="163" r="5" fill="#fe704d" fill-opacity=".35" />
        <path d="M118 166 q8 7 16 0" stroke="#7a2e1f" stroke-width="2.4" stroke-linecap="round" />
        <circle cx="97" cy="160" r="3" fill="#ffd28f" />
        {{-- Pointing arm --}}
        <path d="M160 206 Q192 232 214 268" stroke="#fe704d" stroke-width="16" stroke-linecap="round" />
        <circle cx="218" cy="273" r="9" fill="#e3a77f" />
    </g>

    {{-- Care --}}
    <path d="M206 192 C190 181 192 166 202 168 C205 169 206 172 206 172 C206 172 207 169 210 168 C220 166 222 181 206 192 Z" fill="#fe704d" />

    {{-- Student --}}
    <g>
        <rect x="277" y="200" width="16" height="16" rx="5" fill="#b9805a" />
        <path d="M246 304 C248 250 258 216 285 211 C312 216 322 250 324 304 Z" fill="#ffffff" />
        <path d="M273 213 L285 228 L297 213" stroke="#0f5e5c" stroke-width="5" stroke-linejoin="round" />
        <path d="M282 226 L288 226 L291 250 L285 257 L279 250 Z" fill="#0f5e5c" />
        <circle cx="285" cy="184" r="25" fill="#cf9168" />
        <path d="M260 182 C260 158 276 151 291 153 C305 155 312 168 310 182 C306 172 296 166 284 166 C273 166 265 172 260 182 Z" fill="#1f1714" />
        <path d="M270 176 q5 -3 10 0" stroke="#1f1714" stroke-width="2.2" stroke-linecap="round" />
        <path d="M288 176 q5 -3 10 0" stroke="#1f1714" stroke-width="2.2" stroke-linecap="round" />
        <circle cx="274" cy="185" r="3" fill="#1f1714" />
        <circle cx="292" cy="185" r="3" fill="#1f1714" />
        <path d="M278 196 q7 6 14 0" stroke="#7a2e1f" stroke-width="2.4" stroke-linecap="round" />
        {{-- Writing arm and pencil --}}
        <path d="M258 230 Q248 256 244 274" stroke="#ffffff" stroke-width="14" stroke-linecap="round" />
        <circle cx="244" cy="278" r="8" fill="#cf9168" />
        <path d="M240 284 L254 262" stroke="#ffd28f" stroke-width="4" stroke-linecap="round" />
        <path d="M314 232 Q322 258 314 278" stroke="#ffffff" stroke-width="14" stroke-linecap="round" />
        <circle cx="313" cy="282" r="8" fill="#cf9168" />
    </g>

    {{-- Desk, books and pencil cup --}}
    <rect x="48" y="300" width="304" height="76" rx="8" fill="#e9d3b4" />
    <rect x="30" y="288" width="340" height="16" rx="8" fill="#f6e7d4" />
    <path d="M186 289 Q211 279 236 286 L236 296 Q211 290 186 299 Z" fill="#ffffff" />
    <path d="M236 286 Q261 279 286 289 L286 299 Q261 290 236 296 Z" fill="#fdf3e4" />
    <path d="M236 286 L236 296" stroke="#e9d3b4" stroke-width="2" />
    <path d="M196 290 Q211 285 228 288 M246 288 Q261 285 276 290" stroke="#d6c2a5" stroke-width="1.5" stroke-linecap="round" />
    <rect x="52" y="276" width="56" height="12" rx="3" fill="#45c2b5" />
    <rect x="58" y="264" width="48" height="12" rx="3" fill="#fe704d" />
    <rect x="55" y="252" width="44" height="12" rx="3" fill="#ffd28f" />
    <rect x="330" y="262" width="22" height="26" rx="5" fill="#45c2b5" />
    <path d="M336 262 L333 246 M341 262 L342 242 M346 262 L350 248" stroke="#ffd28f" stroke-width="3" stroke-linecap="round" />
</svg>
