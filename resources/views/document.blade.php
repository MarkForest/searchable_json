<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Document Search</title>
</head>
<body>
<div class="container">
    <h1>Cбалансированное дерево поиска</h1>
    @if(session('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="card">
        <div class="card-body">
            <div class="row">
                <form action="{{ route('document.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="input-group">
                        <input type="file" class="form-control" id="inputGroupFile04"
                               aria-describedby="inputGroupFileAddon04" aria-label="Upload" name="file" accept=".json"
                               required>
                        <button class="btn btn-outline-primary" type="submit" id="inputGroupFileAddon04">Загрузить
                            дерево поиска
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('document.search') }}" method="post">
                @csrf
                <div class="input-group mb-3">
                    <div class="input-group-text">
                        <label class="label label-sm me-3">Бинарный поиск</label>
                        <input name="isIndexSearch" id="search" class="form-check-input mt-0" type="checkbox" value="true" @if($isIndexSearch) checked @endif
                               aria-label="Checkbox for following text input">
                    </div>
                    <input type="text" class="form-control" aria-label="Text input with checkbox" name="query"
                           value="{{$query ?? old('query')}}">
                    <button class="btn btn-outline-primary" type="submit" id="inputGroupFileAddon04">Поиск</button>
                </div>
            </form>

            @if(isset($comparisons))
                <p>Операций сравнения: {{ $comparisons }}</p>
            @endif

            @if(isset($documents))
                @if($documents)
                    <h1>Documents</h1>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Details</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($documents as $document)
                            @if(isset($document['left']) || isset($document['right']))
                                <tr>
                                    <td>{{ $document['value']['id'] }}</td>
                                    <td>{{ $document['value']['name'] }}</td>
                                    <td>{{ json_encode($document['value'], JSON_THROW_ON_ERROR) }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td>{{ $document['id'] ?? null}}</td>
                                    <td>{{ $document['name'] }}</td>
                                    <td>{{ json_encode($document) }}</td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <p>Документы не найдены.</p>
                @endif
            @else
                <p class="text-secondary h5">Информация о файле...</p>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>
