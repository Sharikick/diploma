<?php

namespace App\Http\Controllers;

use App\Services\DocumentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    private DocumentService $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function history()
    {
        $validations = Auth::user()->validations;
        return view('history', compact('validations'));
    }

    public function upload(Request $request)
    {
        Log::info("Hello world");
        $request->validate([
            "document" => ["required", "mimes:docx"]
        ]);

        try {
            $this->documentService->validate($request->file("document")->getPathname());
        } catch (Exception $e) {
            return back()->withErrors(["error" => $e->getMessage()]);
        }

        return redirect()->route("history");
    }
}
