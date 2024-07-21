<?php

namespace App\Http\Controllers;

use App\Services\SearchIndexTreeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use JsonException;

class DocumentController extends Controller
{
    private SearchIndexTreeService $searchIndexTreeService;

    public function __construct(SearchIndexTreeService $searchIndexTreeService)
    {
        $this->searchIndexTreeService = $searchIndexTreeService;
    }

    public function showForm()
    {
        $documents = $this->getDocuments();
        $isIndexSearch = false;
        return view('document', compact('documents', 'isIndexSearch'));
    }

    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:json',
        ]);

        $file = $request->file('file');
        $contents = file_get_contents($file);
        $documents = json_decode($contents, true);

        Storage::disk('local')->delete('documents.json');
        try {
            Storage::disk('local')->put('documents.json', json_encode($documents, JSON_THROW_ON_ERROR));
        } catch (JsonException $e) {
            Log::error($e->getMessage());
        }

        $this->searchIndexTreeService = new SearchIndexTreeService();

        foreach ($documents as $document) {
            if (isset($document['id'])) {
                $this->searchIndexTreeService->insert($document['id'], $document);
            }
        }

        $this->searchIndexTreeService->saveToFile(storage_path('app/index.json'));
        return redirect()->route('document.form')->with('message', 'Файл загружен и индекс создан.');
    }

    public function search(Request $request)
    {
        if (is_null($request->input('query')) || empty($request->input('query'))) {
            return redirect()->route('document.form');
        }

        $query = $request->input('query');

        if($request->has('isIndexSearch')) {
            $indexFile = storage_path('app/index.json');

            if (!Storage::disk('local')->exists('index.json')) {
                return redirect()->route('document.form')->with('error', 'Индекс не найден.');
            }

            $indexData = json_decode(file_get_contents($indexFile), true, 512, JSON_THROW_ON_ERROR);
            $this->buildTree($indexData);

            $result = $this->searchIndexTreeService->search($query);
            return view('document', [
                'documents' => $result['value'] ? [$result['value']] : [],
                'comparisons' => $result['comparisons'],
                'query' => $query,
                'isIndexSearch' => $request->has('isIndexSearch'),
            ]);
        }

        $documents = $this->getDocuments();
        $comparisons = 0;
        $documentsResult = [];
        foreach ($documents as $document) {
            $comparisons++;
            if (isset($document['id']) && $document['id'] === $query) {
                $documentsResult[] = $document;
                break;
            }
        }
        return view('document', [
            'documents' => $documentsResult ? $documentsResult : [],
            'comparisons' => $comparisons,
            'query' => $query,
            'isIndexSearch' => $request->has('isIndexSearch'),
        ]);
    }

    private function buildTree(array $nodes): void
    {
        if (isset($nodes['key'], $nodes['value'])) {
            $this->searchIndexTreeService->insert($nodes['key'], $nodes['value']);
        }

        if (!empty($nodes['left'])) {
            $this->buildTree($nodes['left']);
        }

        if (!empty($nodes['right'])) {
            $this->buildTree($nodes['right']);
        }
    }

    public function getDocuments()
    {
        if (!Storage::disk('local')->exists('documents.json')) {
            return [];
        }

        try {
            return json_decode(Storage::disk('local')->get('documents.json'), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            Log::error($e->getMessage());
            return [];
        }
    }
}
