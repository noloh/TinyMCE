<?php
//Path to NOLOH Kernel, change to your path
require_once("/var/www/htdocs/Stable/NOLOH/NOLOH.php");
System::IncludePaths('../');

class TinyMCETest3 extends WebPage
{
	function __construct()
	{
		parent::WebPage('Basic TinyMCE Example 3');
		$content = new MarkupRegion('content.txt', 0, 0, 400, null);
		$this->Controls->AddRange($content, new BlogComment());
		$this->Controls->AllLayout = Layout::Relative;
	}
}
class BlogComment extends Panel
{
	function __construct($left = 0, $top = 0, $width = 400, $height = 150)
	{
		parent::Panel($left, $top, $width, $height);
		$this->Controls->Add($comment = new TinyMCE('', 0, 0, $width, $height - 30));
		$this->Controls->Add(new Button('Save'))
			->Click = new ServerEvent($this, 'Save', $comment);
		$this->Controls->AllLayout = Layout::Relative;
	}
	function Save($editor)
	{
		$comment = $editor->Text;
		$editor->Leave();
		$this->Controls->Clear();
		$this->Controls->Add(new MarkupRegion($comment, 0, 0, '100%', '100%'));
		WebPage::That()->Controls->Add(new BlogComment())	
			->Layout = Layout::Relative;
	}
}