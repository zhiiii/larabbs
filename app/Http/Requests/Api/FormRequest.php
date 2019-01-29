<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhi
 * Date: 2019-01-29 16:49
 */

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest as BaseFormRequest;

class FormRequest extends BaseFormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}
}