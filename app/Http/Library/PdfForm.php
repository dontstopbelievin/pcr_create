<?php
namespace App\Http\Library;

class PdfForm
{
	/*
    * Path to raw PDF form
    * @var string
    */
    private $pdfurl;

    /*
    * Form data
    * @var array
    */
    private $data;

    /*
    * Path to filled PDF form
    * @var string
    */
    public $output;

    /*
    * Flag for flattening the file
    * @var string
    */
    private $flatten;

	public function __construct($pdfurl, $data)
	{
	    $this->pdfurl = $pdfurl;
	    $this->data   = $data;
	}

	private function tmpfile()
	{
	    $tmp_files_path = env("TMP_FILES_PATH", '/var/www/pcr_create/public/tmp_files/');
	    return tempnam($tmp_files_path, gethostname());
	}

	public function fields($pretty = false)
	{
	    $tmp = $this->tmpfile();

	    exec("pdftk {$this->pdfurl} dump_data_fields > {$tmp}");
	    $con = file_get_contents($tmp);

	    unlink($tmp);
	    return $pretty == true ? nl2br($con) : $con;
	}

	public function makeFdf($data)
	{

	    $fdf = '%FDF-1.2
	    1 0 obj<</FDF<< /Fields[';

	    foreach ($data as $key => $value) {
	        $fdf .= '<</T(' . $key . ')/V(' . $value . ')>>';
	    }

	    $fdf .= "] >> >>
	    endobj
	    trailer
	    <</Root 1 0 R>>
	    %%EOF";

	    $fdf_file = $this->tmpfile();
	    file_put_contents($fdf_file, $fdf);

	    return $fdf_file;
	}

	public function flatten()
	{
	    $this->flatten = ' flatten';
	    return $this;
	}

	private function generate()
	{

	    $fdf = $this->makeFdf($this->data);
	    $this->output = $this->tmpfile();
	    exec("pdftk {$this->pdfurl} fill_form {$fdf} output {$this->output}{$this->flatten}");

	    unlink($fdf);
	}

	public function save()
	{

	    if (!$this->output) {
	        $this->generate();
	    }

	    return $this;
	}

	public function download()
	{
	    if (!$this->output) {
	        $this->generate();
	    }

	    $filepath = $this->output;
	    if (file_exists($filepath)) {

	        header('Content-Description: File Transfer');
	        header('Content-Type: application/pdf');
	        header('Content-Disposition: attachment; filename=' . uniqid(gethostname()) . '.pdf');
	        header('Expires: 0');
	        header('Cache-Control: must-revalidate');
	        header('Pragma: public');
	        header('Content-Length: ' . filesize($filepath));

	        readfile($filepath);

	        exit;
	    }
	}
}
?>
