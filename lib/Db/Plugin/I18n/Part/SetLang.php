<?php
/*
 * Created by Martin Wernståhl on 2009-10-17.
 * Copyright (c) 2009 Martin Wernståhl.
 * All rights reserved.
 */

class Db_Plugin_I18n_Part_SetLang extends Db_CodeBuilder_Method
{
	function __construct(Db_Descriptor $descriptor)
	{
		$this->name = 'setLang';
		$this->param_list = '$lang_key';
		
		// TODO: Filter?
		$this->addPart('$this->language = $this->db->escape($lang_key);');
	}
}


/* End of file SetLangPart.php */
/* Location: ./lib/Db/Plugin/I18n */