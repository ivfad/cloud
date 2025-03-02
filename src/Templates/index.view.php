<!doctype html>
<html lang="en" class="bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cloud-Storage</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full">
    <div class="bg-white">
        <header class="absolute inset-x-0 top-0 z-50">
            <nav class="flex items-center justify-between p-6 lg:px-8" aria-label="Global">

                <div class="flex lg:flex-1">
                    <a href="/" class="-m-1.5 p-1.5">
                        <span class="sr-only">Cloud-Storage</span>
                        <img class="h-8 w-auto" src="https://tailwindui.com/plus-assets/img/logos/mark.svg?color=indigo&shade=600" alt="">
                    </a>
                </div>

                <?php if($_SESSION['user'] ?? false) :?>

                <div class="lg:flex lg:gap-x-12 ">
                    <a href="/users/list" class="text-sm/6 font-semibold text-gray-900">Users list</a>
                    <?php if($_SESSION['user']['admin'] ?? false) :?>
                            <a href="/admin/users/list" class="text-sm/6 font-semibold text-gray-900">Users list (Admin)</a>
                    <?php endif ?>
                    <a href="/users/update" class="text-sm/6 font-semibold text-gray-900">Update info</a>
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
        </header>
        <div class="relative isolate px-6 pt-14 lg:px-8">
            <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
                <div class="relative left-[calc(50%-11rem)] aspect-1155/678 w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-linear-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>
            <div class="mx-auto max-w-2xl py-32 sm:py-48 lg:py-56">

                <div class="text-center">
                    <h1 class="text-5xl font-semibold tracking-tight text-balance text-gray-900 sm:text-7xl">Cloud-Storage</h1>
                    <p class="mt-8 text-lg font-medium text-pretty text-gray-500 sm:text-xl/8">Store, manage, share</p>
                    <?php if($_SESSION['user'] ?? false) :?>
                    <form action="/users/get/" method="GET" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="id">
                                Get Users Info by Id
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="id" name="id" type="number" placeholder="User's Id">
                        </div>
                        <button class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600" type="submit">Get info</button>
                    </form>
                    <?php else : ?>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        <a href="/login" class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Log in to start</a>
                    </div>
                    <?php endif ?>
                </div>
            </div>
            <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
                <div class="relative left-[calc(50%+3rem)] aspect-1155/678 w-[36.125rem] -translate-x-1/2 bg-linear-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>
        </div>
    </div>

</body>