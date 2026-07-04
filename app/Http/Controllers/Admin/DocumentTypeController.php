<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Yajra\DataTables\Facades\DataTables;

class DocumentTypeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view document types', only: ['index']),
            new Middleware('permission:create document types', only: ['create', 'store']),
            new Middleware('permission:edit document types', only: ['edit', 'update']),
            new Middleware('permission:delete document types', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = DocumentType::query();

        // Real Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $documentTypes = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('admin.document_types.index', compact('documentTypes'));
    }


    public function create()
    {
        return view('admin.document_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'provider_type' => 'required|in:freelancer,business,both',
            'instruction' => 'nullable|string',
        ]);


        DocumentType::create($request->all());

        return redirect()->route('admin.document_types.index')->with('success', 'Document type created successfully.');
    }

    public function edit(DocumentType $documentType)
    {
        return view('admin.document_types.edit', compact('documentType'));
    }

    public function update(Request $request, DocumentType $documentType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'provider_type' => 'required|in:freelancer,business,both',
            'instruction' => 'nullable|string',
        ]);


        $documentType->update($request->all());

        return redirect()->route('admin.document_types.index')->with('success', 'Document type updated successfully.');
    }

    public function destroy(DocumentType $documentType)
    {
        $documentType->delete();
        return redirect()->route('admin.document_types.index')->with('success', 'Document type deleted successfully.');
    }
}
