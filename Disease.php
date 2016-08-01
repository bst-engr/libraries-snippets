<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;


class Disease extends Model {

	//use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'diseases';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['disease_id','disease_name','common_name','description', 'lower_limit','upper_limit'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	//protected $hidden = ['password', 'remember_token'];
	protected $primaryKey = 'disease_id';

	public $errors;

	public $timestamps = false;
	
	public static $rules = array('disease_name'=>'required|string','common_name'=>'required|string','description'=>'required|string', 'lower_limit'=>'required','upper_limit'=>'required');

	 /* The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	

	/*
	*	Funciton is responsibile to validate the form Data
	*/

	public function isValid(){

		// mkaing validations
		$validator = Validator::make($this->attributes, static::$rules);
		
		// check if validation passes
		if($validator->passes()){

			return true;

		} else {

			// setting up error messages.
			$this->errors = $validator->messages();
			return false;
		}
	}

}
