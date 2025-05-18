<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Services\PersonImportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImportController extends Controller
{
    private const MAX_FILE_SIZE = 1024;
    private const ACCEPTED_FILE_TYPES = 'csv,txt';

    public function index(): View
    {
        return view('import', [
            'people' => Person::displayImported()->get(),
        ]);
    }

    public function create(Request $request)
    {
        // TODO: add validation
        $validate = $request->validate([
            'file' => [
                'required',
                'file',
                sprintf('mimes:%s', self::ACCEPTED_FILE_TYPES),
                sprintf('max:%d', self::MAX_FILE_SIZE),
            ],
        ]);

        //        if ($validate->fails()) {
        //            return redirect()->back()->withErrors($validate);
        //        }

        $lastImport = PersonImportService::fromCsv($request->file('file'))->process();

        return view('import', [
            'people' => Person::displayImported()->get(),
            'lastImport' => $lastImport,
        ]);

    }
}
