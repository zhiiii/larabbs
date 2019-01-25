<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhi
 * Date: 2019-01-25 20:52
 */

namespace App\Transformers;


use App\Models\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
	public function transform(Category $category)
	{
		return [
			'id' => $category->id,
			'name' => $category->name,
			'description' => $category->description,
		];
	}
}