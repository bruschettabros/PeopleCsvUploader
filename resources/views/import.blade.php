<!DOCTYPE html>
<head>
    @vite(['resources/css/app.css'])
</head>
<div class="container mt-4">
    <h1 class="mb-4">Street technical test</h1>

    <div class="card mb-4">
        <div class="card-body">
            <h3 class="card-title">Import</h3>
            <form action="{{ route('import.create') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="d-flex gap-2">
                    <input type="file" name="file" accept=".csv" class="form-control">
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>

    @if (isset($lastImport))
        <div class="card mb-4">
            <div class="card-body">
                <h3 class="card-title">Last Import</h3>
                <div>
                    <pre>
                        <code>
                            {{ print_r($lastImport->toDisplay()) }}
                        </code>
                    </pre>
                </div>
            </div>
        </div>
    @endif

    @if (!empty($people))
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">Recent Imports</h3>
                <table class="table table-striped">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Output</th>
                        <th scope="col">Created At</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($people as $index => $person)
                        <tr>
                            <th scope="row">{{ ++$index }}</th>
                            <td>{{ $person->__toString() }}</td>
                            <td>
                                <pre>
                                    <code>
                                        {{ print_r($person->toDisplay()) }}
                                    </code>
                                </pre>
                            </td>
                            <td>{{ $person->created_at }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
