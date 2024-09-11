@extends(Theme::path('orders.master'))

@section('title', 'Files | ' . $order->name)

@section('content')

    <link rel="stylesheet" href="{{ Module::asset('pterodactyl:css/codeeditor/codemirror.min.css') }}">
    <script src="{{ Module::asset('pterodactyl:js/codeeditor/codemirror.min.js') }}"></script>

    <!-- Search/Replace -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.7/addon/search/searchcursor.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.7/addon/search/search.min.js"></script>

    <!-- FullScreen Addon -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.7/addon/display/fullscreen.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.7/addon/display/fullscreen.min.css">

    <!-- Default Themes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/theme/dracula.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/theme/idea.min.css">

    <!-- Default Mode -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/properties/properties.min.js"></script>


    {{-- Files container --}}
    <div class="container mx-auto">
        <div class="relative overflow-x-auto overflow-y-auto shadow-md sm:rounded-lg" style="height: 80vh;">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead
                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 sticky top-0">
                <tr>
                    <th scope="col" class="p-4 ps-3 pe-0">
                        <div class="flex items-center">
                            <input id="checkbox-all-search" type="checkbox"
                                   class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="checkbox-all-search" class="sr-only">checkbox</label>
                        </div>
                    </th>
                    <th scope="col" class="">
                        <div class="">
                            <button data-modal-target="create-directory" data-modal-toggle="create-directory"
                                    class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded">
                                <i class='bx bxs-folder-plus'></i>
                                {!! __('client.create_dir') !!}
                            </button>
                            <button data-modal-target="upload-modal" data-modal-toggle="upload-modal"
                                    class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded">
                                <i class='bx bxs-cloud-upload'></i>
                                {!! __('client.upload') !!}
                            </button>
                            <button id="new-file-modal-btn"
                                    class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded">
                                <i class='bx bx-file-blank'></i>
                                {!! __('client.new_file') !!}
                            </button>
                        </div>
                    </th>
                    <th scope="col" class="text-end"></th>
                    <th scope="col" class="text-end"></th>
                    <th scope="col" class="text-end"></th>
                    <input type="hidden" id="current_path">
                </tr>
                </thead>
                <tbody id="table-body"></tbody>
            </table>
        </div>
    </div>

    {{-- Context menu --}}
    <div id="contextMenu"
         class="hidden absolute z-50 bg-white rounded-md shadow-lg overflow-hidden divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-900 dark:divide-gray-800"></div>


    {{-- A modal window for creating a directory --}}
    <div id="create-directory" tabindex="-1" aria-hidden="true"
         class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-2 w-full max-w-sm max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="p-3 md:p-4">
                    <div class="space-y-3">
                        <div>
                            <label for="new_directory_name"
                                   class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">{{ __('client.name') }}</label>
                            <input type="text" id="new_directory_name"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                   required>
                        </div>
                        <button data-modal-hide="create-directory" type="button" id="new_directory_submit"
                                class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                            {{ __('client.create') }}
                        </button>
                        <button data-modal-hide="create-directory" type="button"
                                class="text-white bg-yellow-700 hover:bg-yellow-800 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-800">
                            {{ __('client.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- A modal window for renaming a file --}}
    <button id="rename-file-btn" data-modal-target="rename-file" data-modal-toggle="rename-file"
            class="hidden"></button>
    <div id="rename-file" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <div class="relative p-2 w-full max-w-sm">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="p-3 md:p-4">
                    <div class="space-y-3">
                        <div>
                            <label for="new_file_name"
                                   class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">{!! __('client.new_name') !!}</label>
                            <input type="text" id="new_file_name"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                   required>
                        </div>
                        <button data-modal-hide="rename-file" type="button" id="rename_submit"
                                class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                            {!! __('client.rename') !!}
                        </button>
                        <button data-modal-hide="rename-file" type="button"
                                class="text-white bg-yellow-700 hover:bg-yellow-800 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-800">
                            {!! __('client.close') !!}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Editor Modal -->
    <button id="edit-modal-btn" data-modal-target="editor-modal" data-modal-toggle="editor-modal"
            class="hidden"></button>
    <div id="editor-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true"
         class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-7xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 id="editor-modal-header-text" class="text-xl font-medium text-gray-900 dark:text-white">
                        <input type="text" id="editor_file_name"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white w-full"
                               placeholder="eula.yml" value="new-file-name.json">
                    </h3>
                    <div class="ml-4">
                        <button data-tooltip-target="tooltip-left" data-tooltip-placement="left" type="button"
                                class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                            {!! __('client.hot_keys') !!}
                        </button>

                        <div id="tooltip-left" role="tooltip"
                             class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                            <ul class="list-disc list-inside text-white">
                                <li><kbd>F11</kbd> + {!! __('client.full_screen') !!}</li>
                                <li><kbd>Ctrl + F</kbd> - {!! __('client.search') !!}</li>
                                <li><kbd>Ctrl + S</kbd> - {!! __('client.save') !!}</li>
                                <li><kbd>Ctrl + G</kbd> - {!! __('client.find_next') !!}</li>
                                <li><kbd>Shift + Ctrl + G</kbd> - {!! __('client.find_previous') !!}</li>
                                <li><kbd>Shift + Ctrl + F</kbd> - {!! __('client.replace') !!}</li>
                                <li><kbd>Shift + Ctrl + R</kbd> - {!! __('client.replace_all') !!}</li>
                                <li><kbd>Ctrl + Z</kbd> - {!! __('client.undo') !!}</li>
                                <li><kbd>Ctrl + Y</kbd> - {!! __('client.redo') !!}</li>
                                <li><kbd>Ctrl + [</kbd> - {!! __('client.decrease_indent') !!}</li>
                                <li><kbd>Ctrl + ]</kbd> - {!! __('client.increase_indent') !!}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-4">
                    <textarea id="editorArea" name="editor" class="w-full"></textarea>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 space-x-3 rtl:space-x-reverse border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button id="editor-modal-save-btn" type="button"
                            class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                        {!! __('client.save') !!}
                    </button>
                    <button id="editor-modal-save-and-close-btn" data-modal-hide="editor-modal" type="button"
                            class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                        {!! __('client.save_and_close') !!}
                    </button>
                    <button id="editor-modal-cancel-btn" data-modal-hide="editor-modal" type="button"
                            class="text-white bg-yellow-700 hover:bg-yellow-800 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-800">
                        {{ __('client.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Modal -->
    <button id="confirm-modal-btn" data-modal-target="confirm-modal" data-modal-toggle="confirm-modal" class="hidden"
            type="button"></button>
    <div id="confirm-modal" tabindex="-1" data-modal-backdrop="static"
         class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <button type="button"
                        class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="confirm-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">{{ __('client.close') }}</span>
                </button>
                <div class="p-4 md:p-5 text-center">
                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    <h3 id="confirm-message-element"
                        class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400"></h3>
                    <button id="confirm-btn-submit" data-modal-hide="confirm-modal" type="button"
                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-4 py-2 text-center me-2">
                        {!! __('client.yes_sure') !!}
                    </button>
                    <button data-modal-hide="confirm-modal" type="button"
                            class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-4 py-2 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                        {{ __('client.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Upload Modal -->
    <button id="upload-modal-btn" data-modal-target="upload-modal" data-modal-toggle="upload-modal" class="hidden"
            type="button"></button>
    <div id="upload-modal" tabindex="-1" data-modal-backdrop="static"
         class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 pt-12 px-5">
                <button type="button"
                        class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="upload-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">{{ __('client.close') }}</span>
                </button>

                <div class="flex items-center justify-center w-full">
                    <label for="upload-modal-input"
                           class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">{!! __('client.click_upload') !!}</span>
                            </p>
                        </div>
                        <form action="#" method="POST" enctype="multipart/form-data">
                            <input id="upload-modal-input" type="file" name="files[]" multiple class="hidden"/>
                        </form>
                    </label>
                </div>

                <div class="p-4 md:p-5 text-center">
                    <button id="upload-modal-submit" data-modal-hide="upload-modal" type="button"
                            class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                        {{ __('client.submit') }}
                    </button>
                    <button data-modal-hide="upload-modal" type="button"
                            class="text-white bg-yellow-700 hover:bg-yellow-800 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-800">
                        {{ __('client.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="upload-status" class="fixed bottom-4 right-4 bg-gray-200 dark:bg-gray-800 shadow-lg rounded-lg p-4 max-w-xs z-50 hidden">
        <div class="flex flex-col items-start">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                {!! __('client.uploading_files') !!}
                <p class="text-xs text-gray-500 dark:text-gray-400">{!! __('client.not_refresh_page_complete') !!}</p>
            </h4>

            <div class="list-disc list-inside text-gray-600 dark:text-gray-300 w-full">
                <hr class="h-px my-1 bg-gray-200 border-0 dark:bg-gray-700">
                <div id="file-upload-list"></div>
            </div>
        </div>
    </div>


    <input type="hidden" id="fileUrl"
           value="{{ route('pterodactyl.files', ['server' => $server['identifier'], 'order' => $order->id]) }}">
    <input type="hidden" id="downloadUrl"
           value="{{ route('pterodactyl.files.download', ['server' => $server['identifier'], 'order' => $order->id]) }}">
    <input type="hidden" id="renameUrl"
           value="{{ route('pterodactyl.files.rename', ['server' => $server['identifier'], 'order' => $order->id]) }}">
    <input type="hidden" id="copyUrl"
           value="{{ route('pterodactyl.files.copy', ['server' => $server['identifier'], 'order' => $order->id]) }}">
    <input type="hidden" id="deleteUrl"
           value="{{ route('pterodactyl.files.delete', ['server' => $server['identifier'], 'order' => $order->id]) }}">
    <input type="hidden" id="compressUrl"
           value="{{ route('pterodactyl.files.compress', ['server' => $server['identifier'], 'order' => $order->id]) }}">
    <input type="hidden" id="decompressUrl"
           value="{{ route('pterodactyl.files.decompress', ['server' => $server['identifier'], 'order' => $order->id]) }}">
    <input type="hidden" id="writeUrl"
           value="{{ route('pterodactyl.files.write', ['server' => $server['identifier'], 'order' => $order->id]) }}">
    <input type="hidden" id="uploadUrl"
           value="{{ route('pterodactyl.files.upload_url', ['server' => $server['identifier'], 'order' => $order->id]) }}">
    <input type="hidden" id="contentUrl"
           value="{{ route('pterodactyl.files.get_content', ['server' => $server['identifier'], 'order' => $order->id]) }}">
    <input type="hidden" id="createDirectory"
           value="{{ route('pterodactyl.files.create_directory', ['server' => $server['identifier'], 'order' => $order->id]) }}">
    <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">
    <input type="hidden" id="orderId" value="{{ $order->id }}">
    <input type="hidden" id="editor-dark-theme" value="dracula">
    <input type="hidden" id="editor-light-theme" value="idea">

    <div class="hidden">
        <div id="translate-Rename"><i class='bx bxs-rename'></i> {!! __('client.rename') !!}</div>
        <div id="translate-Copy"><i class='bx bxs-copy'></i> {!! __('client.copy') !!}</div>
        <div id="translate-Delete"><i class='bx bxs-message-x'></i> {!! __('client.delete') !!}</div>
        <div id="translate-Download"><i class='bx bxs-download'></i> {!! __('client.download') !!}</div>
        <div id="translate-Archive"><i class='bx bxs-archive-in'></i> {!! __('client.archive') !!}</div>
        <div id="translate-Unarchive"><i class='bx bxs-archive-out'></i> {!! __('client.unarchive') !!}</div>
        <div id="translate-confirm-delete">{!! __('client.sure_you_want_delete') !!}</div>
    </div>


    <script src="{{ Module::asset('pterodactyl:js/script.js') }}"></script>
    <script src="{{ Module::asset('pterodactyl:js/codeeditor/editor.js') }}"></script>
    <script src="{{ Module::asset('pterodactyl:js/fileManager.js') }}"></script>
@endsection

