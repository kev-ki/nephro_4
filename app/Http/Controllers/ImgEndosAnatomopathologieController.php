<?php

namespace App\Http\Controllers;

use App\Consultation;
use App\Dossier;
use App\IMGEndosAnatomopathologie;
use App\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ImgEndosAnatomopathologieController extends Controller
{
    public function index()
    {
        return view('image_endosc_anatomo.index');
    }

    public function create()
    {
        return view('image_endosc_anatomo.create');
    }

    public function store(Request $request)
    {
        /*$validation =Validator::make($request->all(), [
            'date' => ['required', 'date'],
            'nature' => ['required'],
            'type' => ['required'],
            'commentaire' => ['required'],
            'nom_explorateur' => ['required'],
            'etablissement_explorateur' => ['required'],
        ]);
        if ($validation->fails()) {
            return redirect()->Back()->withInput()->withErrors($validation);
        }*/

        $iMGEndosAnatomopathologie=new IMGEndosAnatomopathologie();
        $iMGEndosAnatomopathologie->date=$request->date;
        $iMGEndosAnatomopathologie->type=$request->type;
        $iMGEndosAnatomopathologie->nature=$request->nature;
        $iMGEndosAnatomopathologie->nom_explorateur=$request->nom_explorateur;
        $iMGEndosAnatomopathologie->etablissement_explorateur=$request->etablissement_explorateur;
        $iMGEndosAnatomopathologie->commentaire=$request->commentaire;

        if ($iMGEndosAnatomopathologie->save())
        {
            $consult = Consultation::where('id', Session::get('idconsultation'))->first();
            $consult->id_img_endos_anatomo = $iMGEndosAnatomopathologie->id;
            $consult->update();
            Session::flash('message', 'informations enregistrées.');
            Session::flash('alert-class', 'alert-success');
            return back();
        }
        else{
            Session::flash('message', 'Verifier tous les champs SVP!');
            Session::flash('alert-class', 'alert-danger');

            return back();
        }
    }

    public function imageEndoscopieAnat(Request $req, $id)
    {
        $consult = Consultation::where('id', $id)
            ->first();
        //die($consult);
        //die($req->image);
        if ($req->image === 'imagerie') {
            $iea= IMGEndosAnatomopathologie::where('id', $consult->id_img_endos_anatomo)
                ->where('type', 'imagerie')
                ->first();
        }

        if ($req->endoscopie === 'endoscopie') {
            $iea= IMGEndosAnatomopathologie::where('id', $consult->id_img_endos_anatomo)
                ->where('type', 'endoscopie')
                ->first();
        }

        if ($req->anatomopatholigique === 'anatomopatholigique') {
            $iea= IMGEndosAnatomopathologie::where('id', $consult->id_img_endos_anatomo)
                ->where('type', 'anatomopatholigique')
                ->first();
        }

        if ($iea) {
            $doc = Dossier::select('id_patient')
                ->where('numD', $consult->num_dossier)
                ->first();
            $patient = Patient::where('idpatient', $doc->id_patient)
                ->first();

            return view('image_endosc_anatomo.show', compact('consult', 'iea', 'patient'));
        }else {
            Session::flash('message', 'données non existantes pour cette consultation!');
            Session::flash('alert-class', 'alert-danger');

            return back();
        }
    }

    public function edit(IMGEndosAnatomopathologie $iMGEndosAnatomopathologie)
    {
        return view('image_endosc_anatomo.edit',['iMGEndosAnatomopathologie'=>$iMGEndosAnatomopathologie]);
    }

    public function update(Request $request, IMGEndosAnatomopathologie $iMGEndosAnatomopathologie)
    {
        $validation =Validator::make($request->all(), [
            'date' => ['required', 'date'],
            'nature' => ['required'],
            'type' => ['required'],
            'commentaire' => ['required'],
            'nom_explorateur' => ['required'],
            'etablissement_explorateur' => ['required'],
        ]);
        if ($validation->fails()) {
            return redirect()->Back()->withInput()->withErrors($validation);
        }

       /* $iMGEndosAnatomopathologie=new IMGEndosAnatomopathologie();
        $iMGEndosAnatomopathologie->date=$request->date;
        $iMGEndosAnatomopathologie->type=$request->type;
        $iMGEndosAnatomopathologie->nature=$request->nature;
        $iMGEndosAnatomopathologie->commentaire=$request->commentaire;
        $iMGEndosAnatomopathologie->nom_explorateur=$request->nom_explorateur;
        $iMGEndosAnatomopathologie->etablissement_explorateur=$request->etablissement_explorateur;*/

        if ($iMGEndosAnatomopathologie->update())
        {
            Session::flash('message', 'Modifications effectuées.');
            Session::flash('alert-class', 'alert-success');

            return back();
        }
        else{
            Session::flash('message', 'Verifier tous les champs SVP!');
            Session::flash('alert-class', 'alert-danger');

            return back();
        }
    }

    public function destroy(IMGEndosAnatomopathologie $iMGEndosAnatomopathologie)
    {
        $iMGEndosAnatomopathologie->delete();
        return back();
    }
}
