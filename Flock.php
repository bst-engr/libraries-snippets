<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator, DB;


class Flock extends Model {

	//use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'flocks';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['flock_id', 'display_id','breed_name','batch_size','arrival_date','shed_no', 'user_id','comments'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	//protected $hidden = ['password', 'remember_token'];
	protected $primaryKey = 'flock_id';

	public $errors;

	public $timestamps = false;
	
	public static $rules = array('display_id'=>'required','breed_name'=>'required','batch_size'=>'required','arrival_date'=>'required','shed_no'=>'required','user_id');

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

	public function saveToleranceRanges($formData, $flock_id, $isUpdate=false){
		if($isUpdate == false ) {
			
			$toleranceRange=array();
			foreach($formData as $key=>$row){
				if(is_array($row) && $key != 'batch1_size' && $key != 'batch_arrival'){
					$toleranceRange[] = array('flock_id'=>$flock_id,'key'=>$key,'upper_limit'=>$row['upper'],'lower_limit'=>$row['lower']);
				}
			}
			DB::table('flock_tolerance_ranges')->insert($toleranceRange);
		} else {

			$toleranceRange=array();
			foreach($formData as $key=>$row){
				if(is_array($row) && $key != 'batch1_size' && $key != 'batch_arrival'){
					$toleranceRange = array('flock_id'=>$flock_id,'key'=>$key,'upper_limit'=>$row['upper'],'lower_limit'=>$row['lower']);
					DB::table('flock_tolerance_ranges')->where('flock_id','=',$flock_id)->where('key','=',$key)->update($toleranceRange);
				}
			}
			
		}
		return true;
	}

	public function calculateAverageDate($inputData) {
		
		$arrivalDates = $inputData['batch_arrival'];
		$temp = strtotime($inputData['arrival_date']);
		$datesArray=array();
		foreach($arrivalDates as $date ){
			if(!empty($date)){
				$datesArray[] = strtotime($date);
			}
		}
		if(count($datesArray)) {
			$datesSum = array_sum($datesArray);
			$avgDate = $datesSum/count($datesArray);
			$returnDate = date("Y-m-d", $avgDate);
		} else {
			$returnDate = date("Y-m-d", $temp);
		}
		
		return $returnDate;
	}

	public function closeFlock($flockId){
		DB::table('flocks')->where('flock_id','=',$flockId)->update(array('status'=>$flockId));
		return true;
	}

	public function saveBatchDetail($inputData,$flockId, $isUpdate= false) {
		
		$batchSizes = $inputData['batch1_size'];
		$arrivalDates = $inputData['batch_arrival'];
		$batchDetail=array();
		for($i=0; $i< count($inputData['batch1_size']); $i++){
				if(!empty($batchSizes[$i]) && !empty($arrivalDates[$i])){
					$batchDetail[] = array('flock_id'=>$flockId,'batch_size'=>$batchSizes[$i],'arrival_date'=>$arrivalDates[$i]);
				}
		}
		DB::table('flock_batch_detail')->insert($batchDetail);
		
		return true;
	}


	//to
}
