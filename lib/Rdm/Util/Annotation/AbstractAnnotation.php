<?php
/*
 * Created by Martin Wernståhl on 2010-11-24.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
abstract class Rdm_Util_Annotation_AbstractAnnotation implements Rdm_Util_Annotation_AnnotationInterface
{
	public function isInheritable()
	{
		return true;
	}
	
	public function isPropertyAnnotation()
	{
		return true;
	}
	
	public function isClassAnnotation()
	{
		return true;
	}
	
	public function isMethodAnnotation()
	{
		return true;
	}
	
	public function isFunctionAnnotation()
	{
		return true;
	}
}


/* End of file AbstractAnnotation.php */
/* Location: ./lib/Rdm/Util/Annotation */