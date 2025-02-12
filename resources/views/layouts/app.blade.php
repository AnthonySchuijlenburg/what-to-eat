<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>What to eat</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>

    {{-- blade-formatter-disable --}}
    <style type="text/tailwindcss">
        .btn {
            @apply bg-white w-full rounded-md px-4 py-2 text-center font-medium text-slate-500 shadow-sm ring-1 ring-slate-700/10 hover:bg-slate-50 h-10;
        }

        .input {
            @apply shadow-sm appearance-none border w-full py-2 px-3 text-slate-700 focus:outline-none rounded-md border-slate-300;
        }

        .filter-container {
            @apply mb-4 flex space-x-2 rounded-md bg-slate-100 p-2;
        }

        .filter-item {
            @apply flex w-full items-center justify-center rounded-md px-4 py-2 text-center text-sm font-medium text-slate-500;
        }

        .filter-item-active {
            @apply bg-white shadow-sm text-slate-800 flex w-full items-center justify-center rounded-md px-4 py-2 text-center text-sm font-medium;
        }
    </style>
    {{-- blade-formatter-enable --}}
</head>

<body class="container mx-auto mt-10 mb-10 max-w-8xl">
@yield('content')
</body>

</html>
