<?php
include("IScanner.php");
include("ScanResponse.php");

class Scanimage implements IScanner {
	private function CommandLine($scanRequest) {
		$cmd = Config::Scanimage;
		$cmd = $cmd." --depth ".$scanRequest->depth;
		$cmd = $cmd." --resolution ".$scanRequest->resolution;
		$cmd = $cmd." -l ".$scanRequest->left;
		$cmd = $cmd." -t ".$scanRequest->top;
		$cmd = $cmd." -x ".$scanRequest->width;
		$cmd = $cmd." -y ".$scanRequest->height;
		$cmd = $cmd." --format ".$scanRequest->format;
		$cmdsi = " --brightness ".$scanRequest->brightness;
		$cmdsi = $cmdsi." --contrast ".$scanRequest->contrast;
		$cmdsi = $cmdsi." --mode ".$scanRequest->mode;
		$cmdconv = "";
		if ( $scanRequest->mode == "Gray" )  $cmdconv = $cmdconv."  -colorspace Gray ";
		$cmdconv = $cmdconv." -brightness-contrast ".$scanRequest->brightness;
		$cmdconv = $cmdconv."x".$scanRequest->contrast;
		// Last
		if ( empty($scanRequest->outputFilter ))
			$cmd = $cmd.$cmdsi.' >"'.$scanRequest->outputFilepath.'"';
		else
			$cmd = $cmd.' |'. $scanRequest->outputFilter.$cmdconv .' "'.$scanRequest->outputFilepath.'"';
		return $cmd;
	}

	public function Execute($scanRequest) {
		$scanResponse = new ScanResponse();
		$scanResponse->errors = $scanRequest->Validate();
		if (count($scanResponse->errors) == 0) {
			$scanResponse->cmdline = $this->CommandLine($scanRequest);
			System::Execute($scanResponse->cmdline, $scanResponse->output, $scanResponse->returnCode);
			$scanResponse->image = $scanRequest->outputFilepath;
		}

		return $scanResponse;
	}
}
?>
