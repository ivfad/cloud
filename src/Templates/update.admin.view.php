<?php include('blocks/header.block.php'); ?>

<body class="h-full">
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <?php include('blocks/icon.block.php'); ?>
            <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Update user's info</h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <form class="space-y-6" action="/admin/users/update/" method="POST" id="UpdateForm" onsubmit="addInputValue()">
                <input type="hidden" name="_method" value="PUT">
                <div>
                    <label for="update-id" class="block text-sm font-medium leading-6 text-gray-900">ID</label>
                    <div class="mt-2">
                        <input id="update-id" name="id" type="number" placeholder="ID" class="block w-full rounded-md border-0 px-2 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>
                <?php include('blocks/updateFields.block.php'); ?>
                <div>
                    <label for="admin" class="block text-sm font-medium leading-6 text-gray-900">Admin</label>
                    <div class="mt-2">
                        <select id="admin" name="admin" class="block w-full rounded-md border-0 px-2 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option selected disabled></option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>
                <?php include('blocks/submit.block.php'); ?>
            </form>
        </div>
    </div>
    <script defer>
        function addInputValue() {
            const form = document.getElementById('UpdateForm');
            const valueInput = document.getElementById('update-id');

            form.action += valueInput.value;
        }
    </script>
</body>
