<nav class="flex items-center justify-between p-6 lg:px-8" aria-label="Global">
    <div class="flex lg:flex-1">
        <a href="/" class="-m-1.5 p-1.5">
            <span class="sr-only">Cloud-Storage</span>
            <img class="h-8 w-auto" src="https://tailwindui.com/plus-assets/img/logos/mark.svg?color=indigo&shade=600" alt="">
        </a>
    </div>

    <?php if($_SESSION['user'] ?? false) : ?>
        <div class="lg:flex lg:gap-x-12 ">
            <a href="/users/list" class="text-sm/6 font-semibold text-gray-900">Users list</a>

            <?php if($_SESSION['user']['admin'] ?? false) : ?>
                <a href="/admin/users/list" class="text-sm/6 font-semibold text-gray-900">Users list (Expanded)</a>
            <?php endif ?>

            <a href="/users/update" class="text-sm/6 font-semibold text-gray-900">Update self info</a>

            <?php if($_SESSION['user']['admin'] ?? false) : ?>
                <a href="/admin/users/update" class="text-sm/6 font-semibold text-gray-900">Update users info</a>
            <?php endif ?>
        </div>

        <div class="lg:flex lg:flex-1 lg:justify-center">
            <form method="GET" action="/reset_password">
                <button class="text-sm/6 font-semibold text-gray-900">Reset password</button>
            </form>
        </div>

        <div class="flex lg:flex-1 lg:justify-end">
            <div class="relative flex ml-3">
                <button type="button" class="relative flex max-w-xs items-center rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                    <span class="sr-only">Open user menu</span>
                    <img class="h-8 w-8 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=2&amp;w=256&amp;h=256&amp;q=80" alt="">
                </button>
            </div>
            <div class="ml-3">
                <form method="GET" action="/logout">
                    <button class="text-sm/6 font-semibold text-gray-900">Sign Out</button><span aria-hidden="true">&rarr;</span>
                </form>
            </div>
        </div>
    <?php else : ?>
        <div class="lg:flex lg:flex-1 lg:justify-center">
            <a href="/register" class="text-sm/6 font-semibold text-gray-900">Register</a>
        </div>
        <div class="lg:flex lg:flex-1 lg:justify-end">
            <a href="/login" class="text-sm/6 font-semibold text-gray-900">Log in<span aria-hidden="true">&rarr;</span></a>
        </div>
    <?php endif ?>
</nav>