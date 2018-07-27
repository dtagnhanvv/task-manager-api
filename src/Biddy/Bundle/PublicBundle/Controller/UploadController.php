<?php

namespace Biddy\Bundle\PublicBundle\Controller;

use Biddy\Bundle\AdminApiBundle\Handler\UserHandlerInterface;
use Biddy\Bundle\ApiBundle\Controller\RestControllerAbstract;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\Request;

class UploadController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Upload
     *
     * @ApiDoc(
     *  section = "Upload",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param Request $request the request object
     *
     * @return mixed
     */
    public function postUploadAction(Request $request)
    {
        /** @var FileBag $fileBag */
        $fileBag = $request->files;

        $metadata = null;
        $rawMetadata = $request->request->get('metadata', null);
        if (null !== $rawMetadata) {
            $metadata = json_decode($rawMetadata, true);
        }

        return $this->processUploadedFiles($fileBag, $metadata);
    }

    /**
     * @inheritdoc
     */
    protected function getResourceName()
    {
        return 'upload';
    }

    /**
     * @inheritdoc
     */
    protected function getGETRouteName()
    {
        return 'public_1_get_upload';
    }

    /**
     * @return UserHandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('biddy_public.handler.user');
    }

    /**
     * @param $fileBag
     * @param $metadata
     * @throws Exception
     * @return mixed
     */
    private function processUploadedFiles($fileBag, $metadata)
    {
        /** @var UploadedFile[] $files */
        $files = $this->getUploadedFiles($fileBag);
        $uploadRootDir = $this->container->getParameter('upload_file_dir');
        $serverName = $this->container->getParameter('server_name');
        $uploadPath = sprintf("%s/folder_%s", $uploadRootDir, random_int(0, 10000));

        $result = [];

        foreach ($files as $file) {
            if (!($file instanceof UploadedFile)) {
                continue;
            }

            $name = sprintf("%s_%s", uniqid(), $file->getClientOriginalName());
            try {
                $fileUpload = $file->move($uploadPath, $name);
                $link = $this->configLink($fileUpload, $serverName);

                return ["link" => $link];
            } catch (Exception $e) {

            }
        }

        return $result;
    }

    /**
     * @param FileBag $fileBag
     * @return array
     */
    private function getUploadedFiles(FileBag $fileBag)
    {
        $keys = $fileBag->keys();

        $files = array_map(
            function ($key) use ($fileBag) {
                /**@var UploadedFile $file */
                $file = $fileBag->get($key);
                return $file;
            }, $keys
        );

        if (!is_array($files)) {
            return [];
        }

        return array_values(
            array_filter(
                $files,
                function ($file) {
                    return ($file instanceof UploadedFile);
                }
            )
        );
    }

    /**
     * @param File $fileUpload
     * @return mixed
     */
    private function configLink(File $fileUpload, $serverName)
    {
        $paths = explode("web/", $fileUpload->getPathname());

        $link = end($paths);
        $link = sprintf("%s/%s", $serverName, $link);

        return $link;
    }
}
