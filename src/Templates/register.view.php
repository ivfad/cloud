<?php include('blocks/header.block.php'); ?>

<body>
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <?php include('blocks/icon.block.php'); ?>
            <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Register your account</h2>
        </div>
        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <form class="space-y-6" action="/register" method="POST">
                <?php include('blocks/auth.block.php'); ?>
            </form>
        </div>
    </div>
</body>