<?php
namespace App\Controller;
use App\Controller\AppController;
use App\Vendor\qqFileUploader;

class UploadController extends AppController {
	// Method for upload question photo
	public function photo() {
		$this->autoRender = false;
		$response['success'] = false;
		$path = 'uploads' . DS . 'tmp';
		$url = 'uploads/tmp/';
		$filename = $this->request->data['myfile']['tmp_name'];
		list($width, $height, $typeCode) = getimagesize($filename);
		$imageType = ($typeCode == 1 ? "gif" : ($typeCode == 2 ? "jpeg" : ($typeCode == 3 ? "png" : FALSE)));
		$imagePath = 'uploads/tmp/';
		$uniquesavename=time(). '_' .uniqid(rand());
		$destFile = $imagePath . $uniquesavename . '.' . $imageType;
		if (move_uploaded_file($filename,  $destFile)) {
			$response['success'] = true;
			$response['filename'] = $uniquesavename . '.' . $imageType;
		}
		echo json_encode($response);
	}

	public function ajaxVideo($id = null) {
		$this->loadModel('Helps');
		if (!$id) {
			$path = 'uploads' . DS . 'tmp';
			$url = 'uploads/tmp/';
		} else {
			$video = $this->Helps->get($id, ['contain' => []]);
			$path = 'uploads' . DS . 'videos';
			$url = 'uploads/videos/';
		}
		
		$this->_prepareDir($path);
		$allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

		$uploader = new qqFileUploader($allowedExtensions);
		
		// Call handleUpload() with the name of the folder, relative to PHP's getcwd()
		$result = $uploader->handleUpload($path);
		// pr($result);
		// exit;
		
		if (!empty($result['success'])) {
			require_once(ROOT . '/vendor' . DS . 'phpThumb/ThumbLib.inc.php');
			//$phpThumb = new PhpThumbFactory();
			$photo = PhpThumbFactory::create($path . DS . $result['filename'], array('jpegQuality' => 100));
			// // resize image
			// App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
			// $photo = PhpThumbFactory::create($path . DS . $result['filename'], array('jpegQuality' => 100));
			$this->_rotateImage($photo, $path . DS . $result['filename']);
			
			$photo->resize(VIDEO_AVATAR_WIDTH, VIDEO_AVATAR_HEIGHT)->save($path . DS . $result['filename']);
			
			$photo = PhpThumbFactory::create($path . DS . $result['filename']);
			$photo->adaptiveResize(THUMB_WIDTH, THUMB_HEIGHT)->save($path . DS . 't_' . $result['filename']);
			
			if ($id) {
				// save to db
				$video->photo = $result['filename'];
				$this->Helps->save($video);
				
				// delete old files
				if ($video->photo && file_exists($path . DS . $video->photo)) {
					unlink($path . DS . $video->photo);
					unlink($path . DS . 't_' . $video->photo);
				}
			}
			
			$result['avatar'] = $this->request->webroot . $url . $result['filename'];
			$result['filename'] = $result['filename'];
		}
		// to pass data through iframe you will need to encode all html tags
		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	}

	public function _getExtension($filename = null) {
		$tmp = explode('.', $filename);
		$re = array_pop($tmp);
		return $re;
	}
	
	private function _prepareDir($path) {
		$path = WWW_ROOT . $path;
		if (!file_exists($path)) {
			mkdir($path, 0755, true);
			file_put_contents($path . DS . 'index.html', '');
		}
	}
	
	private function _rotateImage(&$photo, $path) { 
		// rotate image if necessary
		$exif = exif_read_data($path);
		
		if (!empty($exif['Orientation'])) {
			switch ($exif['Orientation']) {
				case 8:
					$photo->rotateImageNDegrees(90)->save($path);
					break;
				case 3:
					$photo->rotateImageNDegrees(180)->save($path);
					break;
				case 6:
					$photo->rotateImageNDegrees(-90)->save($path);
					break;
			}
		}
	}
}

?>
