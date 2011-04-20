<?php
/**
*  TinyMCE Nodule Class
*/
class TinyMCE extends TextArea
{
	/**
	*  Default Themes
	*/
	const O2K7 = 'o2k7', O2K7Silver = 'o2k7_silver', O2K7Black = 'o2k7_black';
	/**
	*  Default Themes
	*/
	const Basic = 'simple', Advanced = 'advanced';
	/**
	* Toolbar Strip Items
	*/
	const Anchor = 'anchor',
	BGColor = 'backcolor',
	Blockquote = 'blockquote',
	Bold = 'bold',
	BulletedList = 'bullist',
	Button = 'Button',
	CharMap = 'charmap',
	Checkbox = 'Checkbox',
	Cleanup = 'cleanup',
	Code = 'code',
	Copy = 'copy',
	Cut = 'cut',
	Emotions = 'emotions',
	Font = 'fontselect',
	FontSize = 'fontsizeselect',
	Form = 'Form',
	Format = 'formatselect',
	FullPage = 'fullpage',
	FullScreen = 'fullscreen',
	Help = 'help',
	HiddenField = 'HiddenField',
	HorizontalRule = 'hr',
	Image = 'image',
	ImageButton = 'ImageButton',
	Indent = 'indent',
	Italic = 'italic',
	JustifyCenter = 'justifycenter',
	JustifyFull = 'justifyfull',
	JustifyLeft = 'justifyleft',
	JustifyRight = 'justifyright',
	Link = 'link',
	Media = 'media',
	NewPage = 'newdocument',
	NonBreaking = 'nonbreaking',
	NumberedList = 'numlist',
	Outdent = 'outdent',
	PageBreak = 'pagebreak',
	Paste = 'paste',
	PasteFromWord = 'pasteword',
	PasteText = 'pastetext',
	Preview = 'preview',
	Prints = 'print',
	Radio = 'Radio',
	Redo = 'redo',
	RemoveFormat = 'removeformat',
	Replace = 'replace',
	RowBreak = '/',
	Save = 'Save',
	Search = 'search',
	Select = 'Select',
	SelectAll = 'selectall',
	Seperator = '|',
	ShowBlocks = 'ShowBlocks',
	Source = 'Source',
	SpellChecker = 'spellchecker',
	Strike = 'strikethrough',
	Styles = 'styleselect',
	Subscript = 'sub',
	Superscript = 'sup',
	Table = 'Table',
	Templates = 'template',
	Textarea = 'Textarea',
	TextColor = 'forecolor',
	TextField = 'TextField',
	Underline = 'underline',
	Undo = 'undo',
	Unlink = 'unlink',
	VisualAid = 'visualaid',
	VisualChars = 'visualchars';
	/**
	* Arraylist Holder for Toolbar Strips
	* 
	* @var ImplicitArrayList
	*/
	private $Strips;
	/**
	* Array holder for Configuration options
	* 
	* @var string|array
	*/
	private $Config;
	/**
	* Allows for handling of Toolbar Properties as if they were a real subclass
	* 
	* @var Toolbar Innersugar
	*/
	static $_InToolbar = 'HandleToolbar';
	/**
	* Constructor
	* 
	* @param string $text
	* @param integer $left
	* @param integer $top
	* @param integer $width
	* @param integer $height
	* @return tinyMCE
	*/
	function TinyMCE($text = '', $left=0, $top=0, $width=500, $height=300)
	{
		parent::TextArea($text, $left, $top, $width, $height);
		$this->Strips = new ImplicitArrayList($this, 'AddStrip', 'RemoveStripAt', 'ClearStrips');
		$this->Strips->InsertFunctionName = 'InsertStrip';
		$this->SetDefaults();
	}
	/**
	* Sets the defaults of this tinyMCE instance
	*/
	private function SetDefaults()
	{
		$this->SetConfig('onchange_callback', '_N.TinyMCEBridge');
	}
	/**
	* Returns the raw contents of the tinyMCE
	* @return string
	*/
//	function GetText()	{return $this->TextHolder->Text;}
	/**
	* Sets the Text of the tinyMCE
	* 
	* @param string $text
	*/
	function SetText($text)	
	{
		parent::SetText($text);
		if($this->ShowStatus == Component::Shown)
			ClientScript::Queue($this, "tinymce.get('$this').setContent", array($text));
	}
	function GetStrips()	{return $this->Strips;}
	/**
	* Strips->Add() Delegate. Use $object->Strips->Add() instead of calling this method.
	*/
	function AddStrip($strip)
	{
		$this->Strips->Add($strip, true);
		$this->SetToolbar($this->Strips->Elements);
	}
	/**
	* Strips->Insert() Delegate. Use $object->Strips->Insert() instead of calling this method.
	*/
	function InsertStrip($strip, $index)
	{
		$this->Strips->Insert($strip, true);
		$this->SetToolbar($this->Strips->Elements);
	}
	/**
	* Strips->Remove() Delegate. Use $object->Strips->Remove() instead of calling this method.
	*/
	function RemoveStripAt($index)
	{
		$this->Strips->RemoveAt($index, true);
		$this->SetToolbar($this->Strips->Elements);
	}
	/**
	* Strips->Clear() Delegate. Use $object->Strips->Clear() instead of calling this method.
	*/
	function ClearStrips()
	{
		$this->Strips->Clear(true);
		$this->SetToolbar($this->Strips->Elements);
	}
	/**
	* Set a tinyMCE configuration option. See {@link http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html} for all options.
	* 
	* @param mixed $option
	* @param mixed $value
	*/
	function SetConfig($option, $value)
	{
		$this->Config[$option] = $value;
		$this->Refresh();
	}
	/**
	* Re-renders the tinyMCE instance. In most cases there is no need to call manually.
	* 
	* @param mixed $race Whether to render with race condition safeguard.
	*/
	function Refresh($race = false)
	{
		if($this->ShowStatus == Component::Shown)
		{
			ClientScript::Queue($this,"tinymce.get('$this').destroy");
			$args = ClientScript::ClientFormat($this->Config);
			ClientScript::RaceQueue($this, 
				'tinymce', "var e=new tinymce.Editor('$this', $args);e.render();");
		}
	}
	/**
	* Toolbar InnerSugar Handler
	*/
	function HandleToolbar()
	{
		$args = func_get_args();
		$invocation = InnerSugar::$Invocation;
		$prop = strtolower(InnerSugar::$Tail);
		if($invocation == InnerSugar::Set)
		{
			switch($prop)
			{
				case 'orientation':
					$this->SetConfig('theme_advanced_toolbar_location', $args[0]);
					break;
				case 'alignment':
					$this->SetConfig('theme_advanced_toolbar_align', $args[0]);
					break;
				default: throw new SugarException();
			}
		}
		elseif($invocation == InnerSugar::Get)
		{
			$lookup = array(
				'orientation' => 'theme_advanced_toolbar_location',
				'alignment' => 'theme_advanced_toolbar_align');
			if(isset($this->Config[$lookup[$prop]]))
				return $this->Config[$lookup[$prop]];
		}
	}
	/**
	* Sets the current Skin of your tinyMCE instance. Please note that if you
	* use a skin variant, your theme must be set to Advanced.
	* 
	* Important: If creating your own variant, use skin_variant string structure
	* 
	* @param tinyMCE::O2K7|tinyMCE::O2K7Silver|tinyMCE::O2K7Black|Custom $skin
	*/
	function SetSkin($skin)
	{
		$skin = explode('_', $skin);
		$this->Config['skin'] = $skin[0];
		if(isset($skin[1]))
			$this->Config['skin_variant'] = $skin[1];
		$this->Refresh();
	}
	/**
	* Sets the current Theme of your tinyMCE instance.
	* 
	* @param tinyMCE::Basic|tinyMCE::Advanced|Custom $theme
	*/
	function SetTheme($theme)
	{
		$this->SetConfig('theme', $theme);
	}
	/**
	* Sets the Toolbar of the TinyMCE instance. 
	*
	* If an array of arrays containting Toolbar Items is provided then Toolbar will be set to contain those items. 
	* In most cases this should be done via the Strips->Add syntax, and not directly through ->Toolbar.
	* 
	* @param array(arrays) $toolbar
	*/
	function SetToolbar($toolbar)
	{
		if(is_array($toolbar))
		{
			/*Currently only works with plugins of same name, 
			but will later me modified to support plugins with multiple
			properties, hence the lookup*/
			$pluginLookup = array(
				'emotions'		=> 'emotions',
				'fullpage'		=> 'fullpage',
				'fullscreen'	=> 'fullscreen',
				'media	'		=> 'media',
				'nonbreaking'	=> 'nonbreaking',
				'pagebreak'		=> 'pagebreak',
				'preview'		=> 'preview',
				'print'			=> 'print',
				'spellchecker'	=> 'spellchecker',
				'visualchars'	=> 'visualchars');
				
			$usedPlugins = array();
			$i = 0;
			foreach($toolbar as $strip)
			{
				if(is_array($strip))
				{
					if($intersection = array_intersect($pluginLookup, $strip))
						array_merge($usedPlugins, $intersection);			
					$elements = implode(',', $strip);
					$this->SetConfig('theme_advanced_buttons' . $i++, $elements);
				}
			}
			if($usedPlugins)
				$this->SetConfig('plugins', implode(',', $usedPlugins));
		}	
		$this->Refresh(true);
	}
	function Leave()
	{
//		if($this->ShowStatus == Component::Shown)
			ClientScript::Queue($this,"tinymce.get('$this').remove");
	}
	/**
	* Do not call manually! Override of default Show(). Triggers when tinyMCE instance is initially shown.
	*/
	function Show()
	{
		parent::Show();
		$relativePath = System::GetRelativePath(getcwd(), dirname(__FILE__));
		//Add tinymce script files
		ClientScript::AddSource($relativePath . 'tinymce/jscripts/tiny_mce/tiny_mce.js', false);
		/*Add NOLOH bridge script file*/
		//Debug Version
//		ClientScript::AddSource($relativePath . 'Bridge/bridge.js', false);
		//Minified Version
		ClientScript::AddSource($relativePath . 'Bridge/bridge-min.js', false);
		$args = ClientScript::ClientFormat($this->Config);
		ClientScript::RaceQueue($this, 
			'tinymce', "var e=new tinymce.Editor('$this', $args);e.render();");		
	}
}
?>
