<?php
namespace Renga\Koupload\Controller\Upload;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;

class Index extends Action
{
    protected $resultJsonFactory;
    protected $uploaderFactory;
    protected $mediaDirectory;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->uploaderFactory = $uploaderFactory;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        if (empty($_FILES['files'])) {
            return $result->setData(['success' => false, 'error' => 'No files uploaded.']);
        }

        $filesData = $_FILES['files'];
        $savedFiles = [];

        foreach ($filesData['name'] as $index => $name) {
            $file = [
                'name'     => $filesData['name'][$index],
                'type'     => $filesData['type'][$index],
                'tmp_name' => $filesData['tmp_name'][$index],
                'error'    => $filesData['error'][$index],
                'size'     => $filesData['size'][$index],
            ];

            try {
                $uploader = $this->uploaderFactory->create(['fileId' => $file]);
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt']);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);

                $path = $this->mediaDirectory->getAbsolutePath('koupload/');
                $resultData = $uploader->save($path);
                $savedFiles[] = $resultData['file'];
            } catch (\Exception $e) {
                return $result->setData([
                    'success' => false,
                    'error' => 'Error uploading ' . $file['name'] . ': ' . $e->getMessage()
                ]);
            }
        }

        return $result->setData([
            'success' => true,
            'files' => $savedFiles
        ]);
    }
}
