<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CRUDHelper;
use App\Models\TalehPhotos;
use App\Models\TalehTranslation;
use App\Utils\Traits\CRUD;
use Exception;
use Illuminate\Http\Request;
use App\Models\Taleh;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;

class TalehController extends Controller
{
    use CRUD;
    public function index()
    {
        check_permission('taleh index');
        $talehs = Taleh::with('photos')->get();
        return view('backend.taleh.index', get_defined_vars());
    }

    public function create()
    {
        check_permission('taleh create');
        return view('backend.taleh.create', get_defined_vars());
    }

    public function store(Request $request)
    {
        check_permission('taleh create');
        try {
            $taleh = new Taleh();
            $taleh->photo = upload('taleh', $request->file('photo'));
            $taleh->save();
            foreach (active_langs() as $lang) {
                $translation = new TalehTranslation();
                $translation->locale = $lang->code;
                $translation->taleh_id = $taleh->id;
                $translation->name = $request->name[$lang->code];
                $translation->description = $request->description[$lang->code];
                $translation->save();
            }
            foreach (multi_upload('taleh',$request->file('photos')) as $photo)
            {
                $talehPhoto = new TalehPhotos();
                $talehPhoto->photo = $photo;
                $taleh->photos()->save($talehPhoto);
            };
            alert()->success(__('messages.success'));
            return redirect(route('backend.taleh.index'));
        } catch (Exception $e) {
            alert()->error(__('backend.error'));
            return redirect(route('backend.taleh.index'));
        }
    }

    public function edit(string $id)
    {
        check_permission('taleh edit');
        $taleh = Taleh::where('id', $id)->with('photos')->first();
        return view('backend.taleh.edit', get_defined_vars());
    }

    public function update(Request $request, string $id)
    {
        check_permission('taleh edit');
        try {
            $taleh = Taleh::where('id', $id)->with('photos')->first();
            DB::transaction(function () use ($request, $taleh) {
                if($request->hasFile('photo')){
                    if(file_exists($taleh->photo)){
                        unlink(public_path($taleh->photo));
                    }
                $taleh->photo = upload('taleh',$request->file('photo'));
                }
                if ($request->hasFile('photos')) {
                   foreach (multi_upload('taleh', $request->file('photos')) as $photo) {
                   $talehPhoto = new TalehPhotos();
                   $talehPhoto->photo = $photo;
                   $taleh->photos()->save($talehPhoto);
                   }
                }
                foreach (active_langs() as $lang) {
                   $taleh->translate($lang->code)->name = $request->name[$lang->code];
                   $taleh->translate($lang->code)->description = $request->description[$lang->code];
                }
                $taleh->save();
            });
            alert()->success(__('messages.success'));
            return redirect()->back();
        } catch (Exception $e) {
            alert()->error(__('backend.error'));
            return redirect()->back();
        }
    }

    public function status(Taleh $taleh)
    {
        check_permission('taleh edit');
        $this->changeStatus($taleh);
        return Redirect::back();
    }

    public function delete(Taleh $taleh)
    {
        check_permission('taleh delete');
        $this->delete($taleh);
        return Redirect::back();
    }
}
