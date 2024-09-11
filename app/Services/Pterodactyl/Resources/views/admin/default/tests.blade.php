@extends(AdminTheme::wrapper(), ['title' => __('Debug'), 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
    <div class="row justify-content-center">
        @if(!$forceHttps)
            <div class="col-md-12">
                <div class="alert alert-warning" role="alert">
                    <strong>Warning!</strong> Your panel is not using HTTPS. This is not recommended and can cause
                    issues with some features. Please enable HTTPS in .env file set FORCE_HTTPS=true.
                </div>
            </div>
        @endif
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    Debug
                    <div class="api-block" id="api-block">
                        <div id="apiStatus"></div>
                    </div>
                </div>
                <div class="card-body">


                    <div class="port-block" id="port-block">
                        <div class="input-group mb-3">
                            <input type="text" id="host" name="host" placeholder="example.com" class="form-control"
                                   list="hostList">
                            <datalist id="hostList">
                                @foreach($nodesIps as $nodeIp)
                                    <option value="{{ $nodeIp }}">
                                @endforeach
                            </datalist>

                            <input type="text" id="port" name="port" placeholder="8080" value="8080"
                                   class="form-control">
                            <div class="input-group-append">
                                <button id="checkPortButton" class="btn btn-primary">Check Port</button>
                            </div>
                        </div>
                        <div id="portStatus"></div>
                    </div>

                    <div class="nodes-block" id="nodes-block">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Node</th>
                                <th>Host</th>
                                <th>Port</th>
                                <th class="text-right">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($nodes as $node)
                                <tr>
                                    <td>{{ $node['name'] }}</td>
                                    <td>{{ $node['fqdn'] }}</td>
                                    <td>{{ $node['daemon_listen'] }}</td>
                                    <td class="text-right" id="status-{{ $node['id'] }}"><span class="badge badge-info">Checking...</span></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>




                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            const checkPortButton = document.getElementById('checkPortButton');
            const hostInput = document.getElementById('host');
            const portInput = document.getElementById('port');
            const portStatusDiv = document.getElementById('portStatus');
            const apiStatusDiv = document.getElementById('apiStatus');
            const nodes = Object.values(JSON.parse('{{ json_encode($nodes) }}'.replace(/&quot;/g,'"')));

            // Separate function to check the port
            async function checkPort(host, port) {
                try {
                    const response = await fetch(`{{ route('pterodactyl.debug.port') }}?host=${encodeURIComponent(host)}&port=${encodeURIComponent(port)}`);
                    const result = await response.json();
                    return result.success;
                } catch (error) {
                    console.error('Error fetching the port status', error);
                    return false;
                }
            }

            // Checking ports for each nodes
            for (const node of nodes) {
                const statusElement = document.getElementById(`status-${node.id}`);
                const isSuccess = await checkPort(node.fqdn, node.daemon_listen);

                statusElement.innerHTML = `<span class="badge ${isSuccess ? 'badge-success' : 'badge-danger'}">${isSuccess ? 'Port is open' : 'Port is closed'}</span>`;
            }

            // Event handler for the port check button
            checkPortButton.addEventListener('click', async () => {
                const host = hostInput.value;
                const port = portInput.value;
                const isSuccess = await checkPort(host, port);
                portStatusDiv.innerHTML = `<span class="badge ${isSuccess ? 'badge-success' : 'badge-info'}">${isSuccess ? 'Port is open' : 'Checking...'}</span>`;
            });

            // Check API status
            try {
                const apiResponse = await fetch(`{{ route('pterodactyl.debug.api') }}`);
                const apiResult = await apiResponse.json();
                let apiStatusContent = '<div class="d-flex justify-content-between align-items-center">';

                apiStatusContent += `<span class="badge ${apiResult.url_available ? 'badge-success' : 'badge-danger'}">URL: ${apiResult.url_available ? 'Available' : 'Unavailable'}</span>`;
                apiStatusContent += `<span class="badge ${apiResult.sso_authorized ? 'badge-success' : 'badge-danger'} mx-2">SSO: ${apiResult.sso_authorized ? 'Authorized' : 'Unauthorized'}</span>`;
                apiStatusContent += `<span class="badge ${apiResult.client_api_available ? 'badge-success' : 'badge-danger'}">Client API: ${apiResult.client_api_available ? 'Available' : 'Unavailable'}</span>`;
                apiStatusContent += `<span class="badge ${apiResult.application_api_available ? 'badge-success' : 'badge-danger'} mx-2">Application API: ${apiResult.application_api_available ? 'Available' : 'Unavailable'}</span>`;
                apiStatusContent += '</div>';
                apiStatusDiv.innerHTML = apiStatusContent;
            } catch (error) {
                console.error('Error fetching the API status', error);
                apiStatusDiv.innerHTML = '<span class="badge badge-warning">Error checking API</span>';
            }
        });
    </script>

@endsection
