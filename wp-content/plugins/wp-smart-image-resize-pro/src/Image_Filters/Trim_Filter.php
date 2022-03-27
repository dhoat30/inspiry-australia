<?php

namespace WP_Smart_Image_Resize\Image_Filters;

use \Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;
use WP_Smart_Image_Resize\Image_Meta;

class Trim_Filter implements FilterInterface
{

    /**
     * The image meta helper instance.
     * @var Image_Meta $imageMeta
     */
    protected $imageMeta;

    public function __construct($imageMeta)
    {
        $this->imageMeta = $imageMeta;
    }

    /**
     * Set trimmed image dimensions.
     *
     * @param $image Image
     */
    private function set_new_dimensions($image)
    {
        $this->imageMeta->setMetaItem('_trimmed_width', $image->getWidth());
        $this->imageMeta->setMetaItem('_trimmed_height', $image->getHeight());
    }

    /**
     * @param Image $image
     *
     * @return Image
     */
    public function applyFilter(Image $image)
    {
        $settings = wp_sir_get_settings();

        if (!$settings['enable_trim']) {

            // Chances the trim feature was re-disabled.
            // In this case, we need revert to original dimensions
            // to prevent zoomed image from being stretshed.
            $this->set_new_dimensions($image);

            return $image;
        }

        $feather = (int)apply_filters('wp_sir_trim_feather',  (int)$settings['trim_feather']);
        $tolerance = (int)apply_filters('wp_sir_trim_tolerance', (int)$settings['trim_tolerance']);

        try {
            $image->trim(null, null, $tolerance, $feather);
            $this->set_new_dimensions($image);
        } catch (\Exception $e) {
        }
        return $image;
    }
}
