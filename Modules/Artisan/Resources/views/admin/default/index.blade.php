@extends(AdminTheme::wrapper(), ['title' => 'Artisan', 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
    <div class="container-fluid  mt-5 mb-5" style="max-width: 90%;">
        <div class="row">
            <div class="col-12">
                <div class="d-flex items-center justify-content-between m-1">
                    <h6>{!! __('admin.logs') !!}</h6>
                    <div class="d-flex items-center justify-content-between">
                        <a href="{{ route('artisan.env-editor') }}"
                           class="btn btn-primary btn-sm m-1 ">{{ __('Env') }}</a>
                        <button class="btn btn-primary btn-sm m-1" type="button" data-toggle="modal"
                                data-target="#commands_modal">{!! __('client.command') !!}
                        </button>
                        <a href="{{ route('artisan.admin-debug-toggle') }}"
                           class="btn @if(Cache::get('admin_debug', false)) btn-success @else btn-danger @endif btn-sm m-1 ">{{ __('Admin area debug') }}</a>
                        <a href="{{ route('artisan.clear-logs') }}"
                           class="btn btn-warning btn-sm m-1 ">{{ __('admin.clear_all') }}</a>
                    </div>

                </div>

                <div class="p-3 rounded shadow-lg bg-body" id="scroll-block" style="height: 300px; overflow-y: auto;">
                    <div id="console-output" class="text-success">
                        <!-- Console output goes here -->
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" style="border: none;">
                            <i class="fas fa-greater-than"></i>
                        </span>
                    </div>
                    <input type="text" id="console-input" class="form-control" style="border: none;"
                           placeholder="{!! __('client.type_command') !!}" list="commands-datalist">
                    <div class="input-group-prepend">
                        <span class="input-group-text" style="border: none;">
                            <i class="fas fa-sign-in-alt"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        @foreach(config('artisan.commands') as $command)
            <div class="card col-12 col-sm-6 col-md-4 col-lg-2 m-1">
                <div class="card-header p-1 justify-content-between">
                    {{ __($command['name']) }}
                    <form action="{{ route('artisan.command') }}" method="post">
                        @csrf
                        <input type="hidden" name="command" value="{{ $command['command'] }}">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-play"></i></button>
                    </form>
                </div>
                <div class="card-body p-1">
                    <p class="card-text">{{ __($command['description']) }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Commands Modal -->
    <div class="modal fade" id="commands_modal" tabindex="-1" role="dialog" aria-labelledby="commands_modal_label"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commands_modal_label"></h5>
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="overflow-x: scroll; height: 80vh;">
                    @foreach($commandsList as $command)
                        <div class="card mb-2">
                            <div class="card-body">
                                <p class="card-title"><code>{{ $command->getName() }}</code> ({{ $command->getDescription() }})</p>
                                @if($command->getDefinition()->getArguments())
                                    <ul>
                                        @foreach($command->getDefinition()->getArguments() as $argument)
                                            <li><code>{{ $argument->getName() }}</code>  ({{ $argument->getDescription() }})</li>
                                        @endforeach
                                    </ul>
                                @endif
                                <div class="text-right">
                                    <button class="btn btn-sm btn-primary insert-command"
                                            data-command="{{ $command->getName() }}">
                                        <i class="fas fa-paste"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    <script>
        class CommandHistory {
            commandHistory;
            historyActivated;
            commandIndex;

            constructor() {
                this.commandHistory = JSON.parse(localStorage.getItem('artisanCommandHistory')) || [];
                this.commandIndex = -1;
                this.historyActivated = false;
            }

            addCommand(command) {
                const existingCommandIndex = this.commandHistory.lastIndexOf(command);
                if (existingCommandIndex > -1) {
                    this.commandHistory.splice(existingCommandIndex, 1);
                }
                this.commandHistory.push(command);
                this.commandIndex = -1;
                this.historyActivated = false;
                localStorage.setItem('artisanCommandHistory', JSON.stringify(this.commandHistory));
            }

            navigateUp() {
                if (!this.historyActivated) {
                    this.historyActivated = true;
                    this.commandIndex = 0;
                } else if (this.commandIndex < this.commandHistory.length - 1) {
                    this.commandIndex++;
                }
                return this.commandHistory[this.commandHistory.length - 1 - this.commandIndex];
            }

            navigateDown() {
                if (this.commandIndex > 0) {
                    this.commandIndex--;
                    return this.commandHistory[this.commandHistory.length - 1 - this.commandIndex];
                } else {
                    this.historyActivated = false;
                    return '';
                }
            }

            clearHistory() {
                this.commandHistory = [];
                this.commandIndex = -1;
                this.historyActivated = false;
                localStorage.setItem('artisanCommandHistory', JSON.stringify(this.commandHistory));
            }
        }

        const commandHistory = new CommandHistory();
        const consoleOutput = document.getElementById('console-output');
        const scrollBlock = document.getElementById('scroll-block');
        const consoleInput = document.getElementById('console-input')
        let lastScrollHeight = 0;
        consoleInput.addEventListener('keyup', function (e) {
            if (e.key === 'Enter') {
                commandHistory.addCommand(consoleInput.value);

                fetch('{{ route('artisan.command-api') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        command: consoleInput.value
                    })
                })
                consoleInput.value = '';
            } else if (e.key === 'ArrowUp') {
                consoleInput.value = commandHistory.navigateUp();
            } else if (e.key === 'ArrowDown') {
                consoleInput.value = commandHistory.navigateDown();
            }
        });

        document.querySelectorAll('.insert-command').forEach(function (button) {
            button.addEventListener('click', function () {
                consoleInput.value = this.getAttribute('data-command');
                $('#commands_modal').modal('hide');
            });
        });

        function fetchConsoleOutput() {
            fetch('{{ route('artisan.read-logs') }}')
                .then(response => response.json())
                .then(data => {
                    data.logs = data.logs.replace(/(\r\n|\n|\r){2,}/g, '\n').replace(/\n/g, '<br>').trim();
                    let consoleLastOutput = consoleOutput.innerHTML;
                    if (data.logs.length > consoleLastOutput.length) {
                        consoleOutput.innerHTML = data.logs;
                    }
                });
        }


        setInterval(function () {
            fetchConsoleOutput();
            if (scrollBlock.scrollHeight > lastScrollHeight) {
                scrollBlock.scrollTop = scrollBlock.scrollHeight;
                lastScrollHeight = scrollBlock.scrollHeight;
            }
        }, 1000);
    </script>
@endsection
