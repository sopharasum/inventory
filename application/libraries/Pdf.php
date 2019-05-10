<?php
	require_once 'dompdf/autoload.inc.php';

	use Dompdf\Dompdf;

	class PDF extends DomPdf
	{
		public function __construct()
		{
			parent::__construct();
		}
	}
?>