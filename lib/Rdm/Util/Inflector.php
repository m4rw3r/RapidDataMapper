<?php

/*
 * Modifications made by Martin Wernstahl:
 * - Class name change to Rdm_... to prevent collisions
 * - Added static public in front of the methods (PHP 6 gives errors, PHP 5 gives warnings when they are missing)
 * - Refactored the code to make it easier to maintain
 * - Added checks for already converted irregular nouns
 */

/**
 * Inflector Library for CodeIgniter
 *
 * Thanks to Akelos Framework, Zend and Ruby on Rails.
 *
 *
 * @author Jason Hamilton-Mascioli <hamiltonmascioli at Gmail dot com>
 * @copyright Copyright (c) 2008, Custom-Mod Solutions Inc., http://www.custom-mod.com
 * @license GNU Lesser General Public License <http://www.gnu.org/copyleft/lesser.html>
 * @since 0.1
 * @version $Revision 0.1 $
 */
class Rdm_Util_Inflector
{
	/**
	 * A list containing singular => plural regexes.
	 * 
	 * @var array
	 */
	public static $plural = array(
		'/(s)tatus$/i' => '\1\2tatuses',
		'/(quiz)$/i' => '\1zes',
		'/^(ox)$/i' => '\1\2en',
		'/([m|l])ouse$/i' => '\1ice',
		'/(matr|vert|ind)(ix|ex)$/i'  => '\1ices',
		'/(x|ch|ss|sh)$/i' => '\1es',
		'/([^aeiouy]|qu)y$/i' => '\1ies',
		'/(hive)$/i' => '\1s',
		'/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',
		'/sis$/i' => 'ses',
		'/([ti])um$/i' => '\1a',
		'/(p)erson$/i' => '\1eople',
		'/(m)an$/i' => '\1en',
		'/(c)hild$/i' => '\1hildren',
		'/(buffal|tomat)o$/i' => '\1\2oes',
		'/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|vir)us$/i' => '\1i',
		'/us$/' => 'uses',
		'/(alias)$/i' => '\1es',
		'/(ax|cri|test)is$/i' => '\1es',
		'/s$/' => 's',
		'/^$/' => '',
		'/$/' => 's'
	);
	
	/**
	 * A list containing plural => singular regexes.
	 * 
	 * @var array
	 */
	public static $singular = array(
		 '/(s)tatuses$/i' => '\1\2tatus',
		'/^(.*)(menu)s$/i' => '\1\2',
		'/(quiz)zes$/i' => '\\1',
		'/(matr)ices$/i' => '\1ix',
		'/(vert|ind)ices$/i' => '\1ex',
		'/^(ox)en/i' => '\1',
		'/(alias)(es)*$/i' => '\1',
		'/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|viri?)i$/i' => '\1us',
		'/(cris|ax|test)es$/i' => '\1is',
		'/(shoe)s$/i' => '\1',
		'/(o)es$/i' => '\1',
		'/ouses$/' => 'ouse',
		'/uses$/' => 'us',
		'/([m|l])ice$/i' => '\1ouse',
		'/(x|ch|ss|sh)es$/i' => '\1',
		'/(m)ovies$/i' => '\1\2ovie',
		'/(s)eries$/i' => '\1\2eries',
		'/([^aeiouy]|qu)ies$/i' => '\1y',
		'/([lr])ves$/i' => '\1f',
		'/(tive)s$/i' => '\1',
		'/(hive)s$/i' => '\1',
		'/(drive)s$/i' => '\1',
		'/([^fo])ves$/i' => '\1fe',
		'/(^analy)ses$/i' => '\1sis',
		'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
		'/([ti])a$/i' => '\1um',
		'/(p)eople$/i' => '\1\2erson',
		'/(m)en$/i' => '\1an',
		'/(c)hildren$/i' => '\1\2hild',
		'/(n)ews$/i' => '\1\2ews',
		'/^(.*us)$/' => '\\1',
		'/s$/i' => ''
	);
	
	/**
	 * A list containing irregular words, singular => plural.
	 * 
	 * @var array
	 */
	public static $irregular = array(
		'atlas' => 'atlases',
		'beef' => 'beefs',
		'brother' => 'brothers',
		'child' => 'children',
		'corpus' => 'corpuses',
		'cow' => 'cows',
		'ganglion' => 'ganglions',
		'genie' => 'genies',
		'genus' => 'genera',
		'graffito' => 'graffiti',
		'hoof' => 'hoofs',
		'loaf' => 'loaves',
		'man' => 'men',
		'money' => 'monies',
		'mongoose' => 'mongooses',
		'move' => 'moves',
		'mythos' => 'mythoi',
		'numen' => 'numina',
		'occiput' => 'occiputs',
		'octopus' => 'octopuses',
		'opus' => 'opuses',
		'ox' => 'oxen',
		'penis' => 'penises',
		'person' => 'people',
		'sex' => 'sexes',
		'soliloquy' => 'soliloquies',
		'testis' => 'testes',
		'trilby' => 'trilbys',
		'turf' => 'turfs'
	);
	
	/**
	 * A list containing uncountable words, regex without slashes at the ends.
	 * 
	 * @var array
	 */
	public static $uncountable = array(
		'.*[nrlm]ese',
		'.*deer',
		'.*fish',
		'.*measles',
		'.*ois',
		'.*pox',
		'.*sheep',
		'amoyese',
		'bison',
		'borghese',
		'bream',
		'breeches',
		'britches',
		'buffalo',
		'cantus',
		'carp',
		'chassis',
		'clippers',
		'cod',
		'coitus',
		'congoese',
		'contretemps',
		'corps',
		'debris',
		'diabetes',
		'djinn',
		'eland',
		'elk',
		'equipment',
		'faroese',
		'flounder',
		'foochowese',
		'gallows',
		'genevese',
		'genoese',
		'gilbertese',
		'graffiti',
		'headquarters',
		'herpes',
		'hijinks',
		'hottentotese',
		'information',
		'innings',
		'jackanapes',
		'kiplingese',
		'kongoese',
		'lucchese',
		'mackerel',
		'maltese',
		'media',
		'mews',
		'moose',
		'mumps',
		'nankingese',
		'news',
		'nexus',
		'niasese',
		'pekingese',
		'people',
		'piedmontese',
		'pincers',
		'pistoiese',
		'pliers',
		'portuguese',
		'proceedings',
		'rabies',
		'rice',
		'rhinoceros',
		'salmon',
		'sarawakese',
		'scissors',
		'sea[- ]bass',
		'series',
		'shavese',
		'shears',
		'siemens',
		'species',
		'swine',
		'testes',
		'trousers',
		'trout',
		'tuna',
		'vermontese',
		'wenchowese',
		'whiting',
		'wildebeest',
		'yengeese'
	);
	
	/**
	 * Pluralizes English nouns.
	 *
	 * @param  string	English noun to pluralize
	 * @return string	Plural noun
	 */
	static public function pluralize($word)
	{
		$lowercased_word = strtolower($word);
		
		foreach(self::$uncountable as $_uncountable)
		{
			if(substr($lowercased_word, (-1 * strlen($_uncountable))) == $_uncountable)
			{
				return $word;
			}
		}
		
		// is it already an irregular plural?
		foreach(array_values(self::$irregular) as $_plural)
		{
			if(substr($lowercased_word, (-1 * strlen($_plural))) == $_plural)
			{
				return $word;
			}
		}
		
		foreach(self::$irregular as $_singular => $_plural)
		{
			if(preg_match('/('.$_singular.')$/i', $word, $arr))
			{
				return preg_replace('/('.$_singular.')$/i', substr($arr[0],0,1).substr($_plural, 1), $word);
			}
		}
		
		foreach(self::$plural as $rule => $replacement)
		{
			if(preg_match($rule, $word))
			{
				$word = preg_replace($rule, $replacement, $word);
				break;
			}
		}
		
		return $word;
	}

	/**
	 * Singularizes English nouns.
	 * 
	 * @param  string	English noun to singularize
	 * @return string	Singular noun.
	 */
	static public function singularize($word)
	{
		$lowercased_word = strtolower($word);
		
		foreach(self::$uncountable as $_uncountable)
		{
			if(substr($lowercased_word, (-1 * strlen($_uncountable))) == $_uncountable)
			{
				return $word;
			}
		}
		
		// is it already an irregular singular?
		foreach(array_keys(self::$irregular) as $_singular)
		{
			if(substr($lowercased_word, (-1 * strlen($_singular))) == $_singular)
			{
				return $word;
			}
		}
		
		foreach(self::$irregular as $_singular => $_plural)
		{
			if(preg_match('/('.$_plural.')$/i', $word, $arr))
			{
				return preg_replace('/('.$_plural.')$/i', substr($arr[0], 0, 1).substr($_singular, 1), $word);
			}
		}
		
		foreach(self::$singular as $rule => $replacement)
		{
			if(preg_match($rule, $word))
			{
				return preg_replace($rule, $replacement, $word);
			}
		}
		
		return $word;
	}
}

/* End of file Inflector.php */
/* Location: ./lib/Rdm/Util */