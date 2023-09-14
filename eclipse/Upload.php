<?php
namespace Eclipse;

class Upload {
    private $allowTypes = ['*'];
    private $uploadDir = '';
    private $uuid = false;

    public function setAllowTypes($types) {
        $this->allowTypes = $types;
    }

    public function setUploadDir($dir) {
        $this->uploadDir = $dir . '/';
    }

    public function setUUID($unique) {
        $this->uuid = $unique;
    }
	public function save($file, $dir) {
        if (! $this->checkFileType($file)) {
            return "Sorry, only WEBP, JPG, JPEG, PNG & GIF files are allowed.";
        }
        if ($dir) $dir .= '/';
        $upload_dir = $this->uploadDir . $dir;
        $filename = basename($file["name"]);
        if ($this->uuid) {
            $filename = $this->generateUUID() . '.' . explode('.', $filename)[1];
        }
        $target_file = $upload_dir . $filename;
        if (! is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $move = move_uploaded_file($file["tmp_name"], $target_file);
        return site_url() . '/' . $target_file;
	}

    private function checkFileType($file) {
        if ($this->allowTypes[0] == '*') {
            return true;
        }
        foreach ($this->allowTypes as $type) {
            if(strpos($file["type"], $type . '/') === 0) {
                return true;
            }
        }
        return false;
    }

	public function resize($image, $destination, $size = "440:400") {
        $type = exif_imagetype($image);
        $src = false;
        switch ($type) {
            case IMAGETYPE_JPEG:
                $src = imagecreatefromjpeg($image);
                break;
            case IMAGETYPE_PNG:
                $src = imagecreatefrompng($image);
                break;
            case IMAGETYPE_WEBP:
                $src = imagecreatefromwebp($image);
                break;
            default:
                break;
        }
        if (! $src) return false;
        $ratio = explode(':', $size);
        if (! is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0777, true);
        }
        
        $wph = $ratio[0] / $ratio[1];
        list($width, $height) = getimagesize($image);
        if ($width >= $height * $wph) {
            $crop = imagecrop($src, ['x' => ($width - $height * $wph)/2, 'y' => 0, 'width' => $height * $wph, 'height' => $height]);
        } else {
            $crop = imagecrop($src, ['x' => 0, 'y' => ($height - $width / $wph)/2, 'width' => $width, 'height' => $width / $wph]);
        }
        $image_p = imagecreatetruecolor($ratio[0], $ratio[0]);
        imagecopyresampled($image_p, $crop, 0, 0, 0, 0, $ratio[0], $ratio[0], min($width, $height), min($width, $height));
        imagewebp($image_p, $destination);
        return $destination;
    }

    public function loadImage($image) {
        if (! file_exists($image)) return false;
        $image_info = getimagesize($image);
        header('Content-Type: ' . $image_info['mime']);
        header('Content-Length: ' . filesize($image));
        readfile($image);
        die();
    }

    private function generateUUID() {
        return uniqid('', true);
    }
}