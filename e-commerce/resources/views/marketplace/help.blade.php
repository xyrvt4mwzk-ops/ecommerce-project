<x-app-layout>
    <x-slot name="header"><x-ui.page-header title="Help / FAQ" subtitle="Quick answers about accounts, purchases, downloads, and creator access." /></x-slot>

    <div class="mx-auto max-w-4xl space-y-4">
        @foreach([
            ['Do I need an account to browse?', 'No. Products, categories, creator storefronts, prices, and public reviews are available to guests.'],
            ['When do I sign in?', 'Google Sign-In is required for cart, wishlist, checkout, reviews, private licenses, and downloads.'],
            ['How are downloads protected?', 'A download is available only to the signed-in owner of an active product license.'],
            ['How do I become a creator or affiliate?', 'Continue with Google, then choose Creator or Affiliate during onboarding. Reviewer and admin roles are managed by administrators.'],
        ] as [$question, $answer])
            <details class="panel group"><summary class="cursor-pointer list-none font-bold text-slate-950">{{ $question }} <span class="float-right text-indigo-600 group-open:rotate-45">+</span></summary><p class="mt-4 text-sm leading-6 text-slate-600">{{ $answer }}</p></details>
        @endforeach
    </div>
</x-app-layout>
