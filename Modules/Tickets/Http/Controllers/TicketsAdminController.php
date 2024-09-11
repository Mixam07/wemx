<?php

namespace Modules\Tickets\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Routing\Controller;
use Modules\Tickets\Entities\Ticket;
use Modules\Tickets\Entities\TicketDepartment as Department;
use Modules\Tickets\Entities\TicketResponder as Responder;
use Modules\Tickets\Entities\TicketResponderKeyword as Keyword;
use Illuminate\Validation\Rule;
use App\Facades\AdminTheme as Theme;
use Illuminate\Http\Request;
use App\Models\Settings;

class TicketsAdminController extends Controller
{
    public function settings()
    {
        return view(Theme::moduleView('tickets','settings'));
    }

    public function viewApiKey() 
    {
        if(!settings('encrypted::tickets::api_key', false)) {
            $api_key = str_random(48);
            Settings::put('encrypted::tickets::api_key', $api_key);
    
            return redirect()->back()->withSuccess("API Key: {$api_key}");
        }

        $api_key = settings('encrypted::tickets::api_key');
        return redirect()->back()->withSuccess("API Key: {$api_key}");
    }

    public function createApiKey() 
    {
        $api_key = str_random(48);
        Settings::put('encrypted::tickets::api_key', $api_key);

        return redirect()->back()->withSuccess("API Key: {$api_key}");
    }

    public function tickets()
    {
        $tickets = Ticket::all();
        $departments = Department::get();

        if(request()->input('department')) {
            $tickets = $tickets->where('department_id', request()->input('department'));
        }

        $nav = 'index';
        return view(Theme::moduleView('tickets','tickets.index'), compact('tickets', 'departments', 'nav'));
    }

    public function openTickets()
    {
        $tickets = Ticket::where('is_open', true)->where('is_locked', false)->get();
        $departments = Department::get();

        if(request()->input('department')) {
            $tickets = $tickets->where('department_id', request()->input('department'));
        }

        $nav = 'open';
        return view(Theme::moduleView('tickets','tickets.index'), compact('tickets', 'departments', 'nav'));
    }

    public function closedTickets()
    {
        $tickets = Ticket::where('is_open', false)->where('is_locked', false)->get();
        $departments = Department::get();

        if(request()->input('department')) {
            $tickets = $tickets->where('department_id', request()->input('department'));
        }

        $nav = 'closed';
        return view(Theme::moduleView('tickets','tickets.index'), compact('tickets', 'departments', 'nav'));
    }

    public function lockedTickets()
    {
        $tickets = Ticket::where('is_locked', true)->get();
        $departments = Department::get();

        if(request()->input('department')) {
            $tickets = $tickets->where('department_id', request()->input('department'));
        }

        $nav = 'locked';
        return view(Theme::moduleView('tickets','tickets.index'), compact('tickets', 'departments', 'nav'));
    }

    public function departments()
    {
        $departments = Department::get();
        return view(Theme::moduleView('tickets', 'departments.index'), compact('departments'));
    }

    public function createDepartment()
    {
        return view(Theme::moduleView('tickets', 'departments.create'));
    }

    public function storeDepartment(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'auto_response_template' => 'nullable',
            'template' => 'nullable',
            'auto_close_after' => 'required',
            'auto_lock_after' => 'required',
        ]);

        Department::create($validatedData);
        return redirect()->route('tickets.departments.index')->withSuccess('Department has been created');
    }

    public function editDepartment(Department $department)
    {
        return view(Theme::moduleView('tickets', 'departments.edit'), compact('department'));
    }

    public function updateDepartment(Department $department, Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'template' => 'nullable',
            'auto_response_template' => 'nullable',
            'auto_close_after' => 'required',
            'auto_lock_after' => 'required',
        ]);

        $department->update($validatedData);
        return redirect()->back()->withSuccess('Department has been updated');
    }

    public function deleteDepartment(Department $department)
    {
        if(Ticket::where('department_id', $department->id)->exists()) {
            $department->update(['is_enabled' => false]);
            return redirect()->route('tickets.departments.index')->withError('The department is already in use by open tickets. The department has been put in inactive state instead');
        }

        $department->delete();
        return redirect()->route('tickets.departments.index')->withSuccess('Department has been deleted');
    }

    public function responders()
    {
        $responders = Responder::get();
        return view(Theme::moduleView('tickets', 'responders.index'), compact('responders'));
    }

    public function createResponder()
    {
        return view(Theme::moduleView('tickets', 'responders.create'));
    }

    public function storeResponder(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'template' => 'required',
        ]);

        Responder::create($validatedData);
        return redirect()->route('tickets.responders.index')->withSuccess('Responder has been created');
    }

    public function editResponder(Responder $responder)
    {
        return view(Theme::moduleView('tickets', 'responders.edit'), compact('responder'));
    }

    public function updateResponder(Responder $responder, Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'template' => 'required',
        ]);

        $responder->update($validatedData);
        return redirect()->back()->withSuccess('Responder has been updated');
    }

    public function deleteResponder(Responder $responder)
    {
        $responder->delete();
        return redirect()->route('tickets.responders.index')->withSuccess('Responder was deleted');
    }

    public function storeKeyword(Responder $responder, Request $request)
    {
        $validatedData = $request->validate([
            'keywords' => 'required',
            'method' => ['required', Rule::in(['contains', 'containsAll'])],
        ]);
    
        $responder->addKeyword($request->input('keywords'), $request->input('method'));
        return redirect()->back();
    }    

    public function deleteKeyword(Keyword $keyword)
    {
        $keyword->delete();
        return redirect()->back();
    }
}
