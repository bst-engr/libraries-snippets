<?php namespace App\Http\Controllers;



use \App\City;

use \App\Developer;

use \App\Listing;

use \App\Project;

use \App\Province;

use \App\Property;

use \App\Inquiry;

use \App\Domain;

use \App\Import;

use Request;

// pagination

use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Pagination\Paginator;



//response and view 

use Response;

use View, DB, Input, Mail;



// custom helper functions



class AdminController extends Controller {



	/*

	|--------------------------------------------------------------------------

	| Home Controller

	|--------------------------------------------------------------------------

	|

	| This controller renders your application's "dashboard" for users that

	| are authenticated. Of course, you are free to change or remove the

	| controller as you wish. It is just here to get your app started!

	|

	*/

	//private $flocks, $user, $dailyFeeding, $standards;

	/**

	 * Create a new controller instance.

	 *

	 * @return void

	 */

	public function __construct()

	{	

		

		$this->middleware('ssg.auth');

		$this->city = New City;

		$this->developer = New Developer ;

		$this->listing = New Listing;	

		$this->project = New Project;

		$this->province = New Province;

		$this->property = New Property;

		$this->inquiry = New Inquiry;

		$this->website = New Domain;

		$this->import = New Import;

	}



	/**

	 * Show the application dashboard to the user.

	 *

	 * @return Response

	 */

	public function index( )
	{	
		if(!\Sentry::getUser()->hasAccess('manage_listings')){
			return redirect()->action('AdminController@properties');
		}

		$listings=  $this->listing->get();
		$website = $this->website->get();
		return view('admin.listings', array('listings'=>$listings, 'websites'=>$website));

	}


/*List Down PRojects with interface to add new*/
	public function project ( ) {
		if(!\Sentry::getUser()->hasAccess('manage_projects')){
			return redirect()->action('AdminController@properties');
		}
		$projects=  $this->project->get();
		$website = $this->website->get();
		return view('admin.projects', array('projects'=>$projects, 'websites'=>$website));

	}


  /*List Down Provinces with interface to add new*/
	public function province ( ) {
		if(!\Sentry::getUser()->hasAccess('manage_provinces')){
			return redirect()->action('AdminController@properties');
		}
		$provinces=  $this->province->get();
		$website = $this->website->get();
		return view('admin.provinces', array('provinces'=>$provinces, 'websites'=>$website));

	}


  /*List Down cities with interface to add new*/
	public function cities ( ) {
		if(!\Sentry::getUser()->hasAccess('manage_cities')){
			return redirect()->action('AdminController@properties');
		}
		$cities=  $this->city->getAllCitiesAdmin();
		$website = $this->website->get();
		$provinces = $this->province->orderBy('province_name', 'ASC')->get();

		return view('admin.cities', array('cities'=>$cities, 'provinces'=>$provinces, 'websites'=>$website));

	}


  /*List Down develoeprs with interface to add new*/
	public function developers ( ) {
		if(!\Sentry::getUser()->hasAccess('manage_developers')){
			return redirect()->action('AdminController@properties');
		}
		$developers=  $this->developer->getAllDevelopersAdmin();
		$website = $this->website->get();
		return view('admin.developers', array('developers'=>$developers, 'websites'=>$website));

	}


  /*List Down Properties with interface to add new*/
	public function properties ( ) {

		$properties=  $this->property->getAllPropertiesAdmin();
		$website = $this->website->get();
		$projects = $this->project->orderBy('project_name','ASC')->get();

		$listings = $this->listing->orderBy('listing_name', 'ASC')->get();

		$provinces = $this->province->orderBy('province_name', 'ASC')->get();

		$developer = $this->developer->orderBy('developer_name', 'ASC')->get();

		return view('admin.properties', array('properties'=>$properties, 'listings'=> $listings, 'provinces'=> $provinces, 'developers'=>$developer, 'projects'=>$projects, 'websites'=>$website));

	}


 /*List Down Inquiries*/
	public function inquiries () {
		if(!\Sentry::getUser()->hasAccess('inquiries')){
			return redirect()->action('AdminController@properties');
		}
		$inquiries= $this->inquiry->getAllInquiries();

		return view('admin.inquiry', array('inquiries'=>$inquiries));	

	}
	
  /*List Down and manage Websites*/
	public function websites () {
		if(!\Sentry::getUser()->hasAccess('manage_websites')){
			return redirect()->action('AdminController@properties');
		}
		$websites= $this->website->get();

		return view('admin.domain', array('domains'=>$websites));	

	}

	

	//Saves Functions Folllowings functions are all related to store processes fetch data from view and pass to model for further processing.

	public function storeListing ( ) {

		$this->listing->fill(Input::all());
		//$this->listing->websites = implode(",", Input::get('websites'));
		

		if ($this->listing->isValid() ) {

        	if(!$this->listing->listing_id || empty($this->listing->listing_id) ){

        		$this->listing->save();

        		

        	} else { //update Flock Information.

        		$this->listing->updateRecord(Input::all());

        	}



            // Success!

        	

            if(empty(Input::get('listing_id')) ) {

            	return $this->listing->listing_id;

	        }else{

	        	return Input::get('listing_id');

	        }

        } else {



            return json_encode($this->listing->errors);

            

        }

	}	



	public function storeProject () {

		$this->project->fill(Input::all());
		//$this->project->websites = implode(",", Input::get('websites'));
		

		if ($this->project->isValid() ) {

        	if(!$this->project->project_id || empty($this->project->project_id) ){

        		$this->project->save();

        		

        	} else { //update Flock Information.

        		$this->project->updateRecord(Input::all());

        	}



            // Success!

        	

            if(empty(Input::get('project_id')) ) {

            	return $this->project->project_id;

	        }else{

	        	return Input::get('project_id');

	        }

        } else {



            return json_encode($this->project->errors);

            

        }

	}



	public function storeProvince () {

		$this->province->fill(Input::all());
		//$this->province->websites = implode(",", Input::get('websites'));
		

		if ($this->province->isValid() ) {

        	if(!$this->province->province_id || empty($this->province->province_id) ){

        		$this->province->save();

        		

        	} else { //update Flock Information.

        		$this->province->updateRecord(Input::all());

        	}



            // Success!

        	

            if(empty(Input::get('province_id')) ) {

            	return $this->province->province_id;

	        }else{

	        	return Input::get('province_id');

	        }

        } else {



            return json_encode($this->province->errors);

            

        }

	}



	public function storeCities () {

		$this->city->fill(Input::all());
		//$this->city->websites = implode(",", Input::get('websites'));
		

		if ($this->city->isValid() ) {

        	if(!$this->city->city_id || empty($this->city->city_id) ){

        		$this->city->save();

        		

        	} else { //update Flock Information.

        		$this->city->updateRecord(Input::all());

        	}



            // Success!

        	

            if(empty(Input::get('city_id')) ) {

            	return $this->city->city_id;

	        }else{

	        	return Input::get('city_id');

	        }

        } else {



            return json_encode($this->city->errors);

            

        }

	}



	public function storeDevelopers () {
		
		$this->developer->fill(Input::all());
		//$this->developer->websites = implode(",", Input::get('websites'));
		

		if ($this->developer->isValid() ) {

        	if(!$this->developer->developer_id || empty($this->developer->developer_id) ){

        		$this->developer->save();

        		

        	} else { //update Flock Information.

        		$this->developer->updateRecord(Input::all());

        	}



            // Success!

        	

            if(empty(Input::get('developer_id')) ) {

            	return $this->developer->developer_id;

	        }else{

	        	return Input::get('developer_id');

	        }

        } else {



            return json_encode($this->developer->errors);

            

        }

	}



	public function storeProperties () {

		$this->property->fill(Input::all());
		//$this->property->websites = implode(",", Input::get('websites'));
		

		if ($this->property->isValid() ) {

        	if(!$this->property->property_id || empty($this->property->property_id) ){

        		$this->property->save();

        		$this->property->savePropertyLibrary(Input::all());

        		

        	} else { //update Flock Information.

        		$this->property->updateRecord(Input::all());

        	}



            // Success!

        	

            if(empty(Input::get('property_id')) ) {

            	return $this->property->property_id;

	        }else{

	        	return Input::get('property_id');

	        }

        } else {



            return json_encode($this->property->errors);

            

        }

	}

	public function storeWebsite () {

		$this->website->fill(Input::all());

		

		if ($this->website->isValid() ) {

        	if(!$this->website->website_id || empty($this->website->website_id) ){

        		$this->website->save();        		

        	} else { //update Flock Information.

        		$this->website->updateRecord(Input::all());

        	}
            // Success!
            if(empty(Input::get('website_id')) ) {

            	return $this->website->website_id;

	        }else{

	        	return Input::get('website_id');

	        }

        } else {
        	return json_encode($this->website->errors);
        }

	}


	//Delete Functions
	public function deleteWebsite ( $id ) {

		$list = $this->website->removeWebsite($id);

		if($list) {

			return '1';

		} else {

			return '0';

		}

	}

	public function deleteListing ( $id ) {

		$list = $this->listing->removeList($id);

		if($list) {

			return '1';

		} else {

			return '0';

		}

	}	



	public function deleteProject ( $id ) {

		$list = $this->project->removeProject($id);

		if($list) {

			return '1';

		} else {

			return '0';

		}

	}



	public function deleteProvince ($id) {

		$list = $this->province->removeProvince($id);

		if($list) {

			return '1';

		} else {

			return '0';

		}

	}



	public function deleteCities ($id) {

		$list = $this->city->removeCity($id);

		if($list) {

			return '1';

		} else {

			return '0';

		}

	}



	public function deleteDevelopers ($id) {

		$list = $this->developer->removeDeveloper($id);

		if($list) {

			return '1';

		} else {

			return '0';

		}

	}



	public function deleteProperties ($id) {

		$list = $this->property->removeProperty($id);

		if($list) {

			return '1';

		} else {

			return '0';

		}

	}

	//Edit Functions
	public function editWebsite ( $id ) {

		$list = $this->website->fetchWebsiteDetails($id);

		echo json_encode($list);

	}

	public function editListing ( $id ) {

		$list = $this->listing->fetchListDetails($id);

		echo json_encode($list);

	}	



	public function editProject ( $id ) {

		$list = $this->project->fetchProjectDetails($id);

		echo json_encode($list);

	}



	public function editProvince ( $id ) {

		$list = $this->province->fetchProvinceDetails($id);

		echo json_encode($list);

	}



	public function editCities ( $id ) {

		$list = $this->city->fetchCityDetails($id);

		echo json_encode($list);

	}



	public function editDevelopers ( $id ) {

		$list = $this->developer->fetchDeveloperDetails($id);

		echo json_encode($list);

	}



	public function editProperties ( $id ) {

		$list = $this->property->fetchPropertyDetails($id);

		echo json_encode($list);

	}



	public function fetchCities ( $id ) {

		$id = explode("_", $id);

		$list = $this->city->fetchProvinceCities($id[0], isset($id[1]) ? $id[1] : false);

		echo $list;	

	}



	public function editCaption ($id) {

		$library = $this->property->getLibrary($id);

		return json_encode($library);

	}



	public function storeCaption () {

		$response = $this->property->savePropertyCaptions(Input::all());

		return $response;

	}

	public function getDataToImport() {
		$response  = $this->import->fetchRelevantData(Input::get('param'));
		echo json_encode($response);
	}

	public function ImportAllProperties() {
		$response  = $this->import->ImportProperties(Input::all());
		echo json_encode($response);	
	}
}

