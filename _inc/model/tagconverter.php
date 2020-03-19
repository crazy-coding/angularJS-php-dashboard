<?php
/*
| -----------------------------------------------------
| PRODUCT NAME: 	Modern POS
| -----------------------------------------------------
| AUTHOR:			ITSOLUTION24.COM
| -----------------------------------------------------
| EMAIL:			info@itsolution24.com
| -----------------------------------------------------
| COPYRIGHT:		RESERVED BY ITSOLUTION24.COM
| -----------------------------------------------------
| WEBSITE:			http://itsolution24.com
| -----------------------------------------------------
*/
class ModelTagconverter extends Model 
{
	public function convert($tags, $objectArr, $message) 
	{
    	if(count($tags)) {
			foreach ($tags as $tag) {
				$rtag = str_replace(array('[',']'), '', $tag);
				if(isset($objectArr[$rtag])) {
					$message = str_replace($tag, $objectArr[$rtag], $message);
				} else {
					$message = str_replace($tag, ' ', $message);
				}
			}
		}
		return $message;
	}
}

// Usage

	// $tc_model = $registry->get('loader')->model('tagconverter');
	// $message = 'Hello, [customer_name]';
	// $message = $tc_model->convert(array('[customer_name]'), array('customer_name' => 'Najmul Hossain'), $message);
	// print_r($message);die;