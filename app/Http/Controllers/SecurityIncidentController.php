<?php

namespace App\Http\Controllers;

use App\Models\SecurityIncident;
use App\Models\SecurityAgency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecurityIncidentController extends Controller
{
    protected function getAgency()
    {
        return Auth::user()->securityAgency;
    }

    public function index()
    {
        $agency = $this->getAgency();
        $incidents = $agency->incidents()->paginate(10);
        return view('security.incidents.index', compact('incidents'));
    }

public function incidents()
{
    return $this->hasMany(SecurityIncident::class, 'agency_id');
}

}