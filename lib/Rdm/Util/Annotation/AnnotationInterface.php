<?php
/*
 * Created by Martin Wernståhl on 2010-11-24.
 * Copyright (c) 2010 Martin Wernståhl.
 * All rights reserved.
 */

/**
 * 
 */
interface Rdm_Util_Annotation_AnnotationInterface
{
	public function isInheritable();
	
	public function isPropertyAnnotation();
	
	public function isClassAnnotation();
	
	public function isMethodAnnotation();
	
	public function isFunctionAnnotation();
}


/* End of file AnnotationInterface.php */
/* Location: ./lib/Rdm/Util/Annotation */