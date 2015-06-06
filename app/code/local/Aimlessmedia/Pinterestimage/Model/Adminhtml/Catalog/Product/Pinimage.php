<?php

class Aimlessmedia_Pinterestimage_Model_Adminhtml_Catalog_Product_Pinimage
{

    public function generatePinterestImage($productId = null)
    {
        if (!is_numeric($productId)) return false;
//Set Media Path
        $mediaPath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS;
        $tmpPath = Mage::getBaseDir('tmp') . DS;

        //aimlessmedia_pinterestimage media directory base
        $baseExtDir="aimlessmedia".DS."pinterestimage".DS;
// Set background image
        $backgroundImgPath = $mediaPath .$baseExtDir. "pingen" . DS . "pin_background.jpg";

// Replace path by your own font path
        $robotoMedium = $mediaPath . $baseExtDir.'fonts' . DS . 'Roboto-Medium.ttf';
        $robotoLight = $mediaPath . $baseExtDir.'fonts' . DS . 'Roboto-Light.ttf';

        $product = Mage::getModel('catalog/product')->load($productId);
        $productImagePath = $mediaPath . 'catalog' . DS . 'product' . $product->getImage();
        $productImageType = exif_imagetype($productImagePath);

//Load the product photo
        switch ($productImageType) {
            case IMAGETYPE_PNG:
                $productImage = imagecreatefrompng($productImagePath);
                break;
            case IMAGETYPE_JPEG:
                $productImage = imagecreatefromjpeg($productImagePath);
                break;
            default :
                return 0;
        }

//Load the background image

        $backgroundImage = imagecreatefromjpeg($backgroundImgPath);

//Get width and height of background image
        list($bgWidth, $bgHeight) = getimagesize($backgroundImgPath);

//Get original width and height of product image
        list($prodWidth, $prodHeight) = getimagesize($productImagePath);

//How much should we scale the width of the product image down?
        $productImageScale = 0.92;

//Get new width
        $newProductImage_Width = $bgWidth * $productImageScale;

//What should we scale the height by now that we know how wide the image is?
        $productImageScale_Percentage = ($newProductImage_Width) / ($prodWidth);
        $newProductImage_Height = ($prodHeight * $productImageScale_Percentage);

        //Generate platform
        $platform = imagecreatetruecolor($bgWidth, $bgHeight);

        //Apply background to platform
        imagecopyresampled($platform, $backgroundImage, 0, 0, 0, 0, $bgWidth, $bgHeight, $bgWidth, $bgHeight);
        $backgroundImage = $platform;

        //Create destination image to copy $productImage to
        $productImage_Resized = imagecreatetruecolor($newProductImage_Width, $newProductImage_Height);
        imagealphablending($productImage_Resized, false);
        imagesavealpha($productImage_Resized, true);

        //Copy $productImage to $productImage_Resized
        imagecopyresampled($productImage_Resized, $productImage, 0, 0, 0, 0, $newProductImage_Width, $newProductImage_Height, $prodWidth, $prodHeight);


// Set the margins for the stamp and get the height/width of the stamp image
        $marge_left = 25;
        $marge_top = 75;


//Create the text container image
        $textContainer = imagecreatetruecolor(400, 30);

// Create some colors
        $green = imagecolorallocate($textContainer, 76, 142, 86);
        $grey = imagecolorallocate($textContainer, 191, 196, 192);
        $black = imagecolorallocate($textContainer, 0, 0, 0);

        $fontSize = 30;
        $headerFontSize = $fontSize * 1.25;
        $titleHeaderFontSize = $fontSize * 1.49;
// Break it up into pieces 125 characters long
// Starting Y position
        $y = $marge_top;
        $glassX = $marge_left;
        $GlassTypeHeaderText = $product->getName();
        $lines = explode('|', wordwrap($GlassTypeHeaderText, 22, '|', 1));
        foreach ($lines as $line) {
            imagettftext($backgroundImage, $titleHeaderFontSize, 0, $glassX - 1, $y + 1, $grey, $robotoMedium, $line);
            imagettftext($backgroundImage, $titleHeaderFontSize, 0, $glassX, $y, $green, $robotoMedium, $line);

            // Increment Y so the next line is below the previous line
            $y += $titleHeaderFontSize * 1.25;
        }

// Copy the stamp image onto our photo using the margin offsets and the photo
// width to calculate positioning of the stamp.
        /*
         * bool imagecopy ( resource $dst_im , resource $src_im , int $dst_x , int $dst_y , int $src_x , int $src_y , int $src_w , int $src_h )
            dst_im=Destination image link resource.
            src_im=Source image link resource.
            dst_x=x-coordinate of destination point.
            dst_y=y-coordinate of destination point.
            src_x=x-coordinate of source point.
            src_y=y-coordinate of source point.
            src_w=Source width.
            src_h=Source height.
         */
        $marge_top = $y - 25;


//Merge the base background image with the product image
        imagecopyresampled($backgroundImage, $productImage_Resized, $marge_left, $marge_top, 0, 0, imagesx($productImage_Resized), imagesy($productImage_Resized), imagesx($productImage_Resized), imagesy($productImage_Resized));

//Free Up memory
        imagedestroy($productImage_Resized);

        if ($product->getGlassRecommended()) {
            $y = $newProductImage_Height + ($marge_top * 1.5);
            $glassX = 40;
            $GlassTypeHeaderText = 'Glass Type:';
            imagettftext($backgroundImage, $headerFontSize, 0, $glassX - 1, $y + 1, $grey, $robotoMedium, $GlassTypeHeaderText);
            imagettftext($backgroundImage, $headerFontSize, 0, $glassX, $y, $green, $robotoMedium, $GlassTypeHeaderText);

            $GlassTypeText = $product->getAttributeText('glass_recommended');
            $lines = explode('|', wordwrap($GlassTypeText, 16, '|', 1));
            foreach ($lines as $line) {
                imagettftext($backgroundImage, $headerFontSize, 0, $glassX + 275 - 1, $y + 1, $grey, $robotoLight, $line);
                imagettftext($backgroundImage, $headerFontSize, 0, $glassX + 275, $y, $green, $robotoLight, $line);

                // Increment Y so the next line is below the previous line
                $y += 50;
            }
            $xStart = $marge_left;
            $yStart = $y - 10; //pickup where our text left off
            $heightOfDivider = $yStart + 5;
            $xEnd = $newProductImage_Width; //we want it as wide as our image
            $yEnd = $heightOfDivider;
            imagefilledrectangle($backgroundImage, $xStart, $yStart, $xEnd, $yEnd, $green);

            $y = $yEnd + (40 * 2);
        } else {
            $y = $newProductImage_Height + ($marge_top * 1.3);
        }

        /*Begin Ingredients*/

        if ($product->getIngredients()) {
            $GlassTypeHeaderText = 'Ingredients:';
            imagettftext($backgroundImage, $headerFontSize, 0, $glassX - 1, $y + 1, $grey, $robotoMedium, $GlassTypeHeaderText);
            imagettftext($backgroundImage, $headerFontSize, 0, $glassX, $y, $green, $robotoMedium, $GlassTypeHeaderText);
            $y += 60;

            $ingredients = $product->getResource()->getAttribute('ingredients')->getFrontend()->getValue($product);
//lets convert linebreaks to "/n"
            $ingredientsConvertedBRstoNL = preg_replace('#<br\s*/?>#i', "\n", $ingredients);
//Lets remove all HTML
            $ingredientsWithoutHtml = strip_tags($ingredientsConvertedBRstoNL);
//Let's remove excess whitespace and lines
            $ingredients = preg_replace('/[ \t]+/', ' ', preg_replace('/\s*$^\s*/m', "\n", $ingredientsWithoutHtml));
//Convert text to ingredient array by new lines
            $ingredientsArray = explode("\n", $ingredients);
//Any array elements that are empty or have whitespace are trimmed
            $ingredientsArray = array_map('trim', $ingredientsArray);
//Now remove any empty array values
            $ingredientsArray = array_diff($ingredientsArray, array(''));
            /* $ingredients = [
                "Ice", "1 oz. Aimlessmedia Tiki Blend", "1 oz. dry gin", "1/2 oz orange liqueur", "1/2 oz. fresh lime juice", "2 oz. pineapple juice", "2 dashes bitters"
            ];*/
            foreach ($ingredientsArray as $ingredient) {
                $linesOfIngredients[] = explode('|', wordwrap($ingredient, 35, '|'));
            }

// Loop through the lines and place them on the image
            foreach ($linesOfIngredients as $recipeKey => $recipeIngredient) {
                //We want to know in our next foreach if we have moved on to the next ingredient
                $ingredientCounter = 1;
                //How many ingredients are there?
                $totalIngredients = count($recipeIngredient);
                //Which line of this ingredient we on
                $line = 1;
                foreach ($recipeIngredient as $recipeLine) {
                    //Total lines for this ingredient
                    $totalIngredientLines = count($recipeIngredient);
                    $x = 40;
                    if ($line > 1) $x += 35;
                    imagettftext($backgroundImage, $fontSize, 0, $x - 1, $y + 1, $grey, $robotLight, $recipeLine);
                    imagettftext($backgroundImage, $fontSize, 0, $x, $y, $green, $robotoLight, $recipeLine);

                    // Increment Y so the next line is below the previous line
                    if ($line !== $totalIngredientLines) {
                        $y += $fontSize * 1.3;
                    }
                    $line++;
                }
                $y += $fontSize * 1.7;
                $ingredientCounter++;
            }
        }
        $xStart = $marge_left;
        $yStart = $y - 10; //pickup where our text left off
        $heightOfDivider = $yStart + 5;
        $xEnd = $newProductImage_Width; //we want it as wide as our image
        $yEnd = $heightOfDivider;
        imagefilledrectangle($backgroundImage, $xStart, $yStart, $xEnd, $yEnd, $green);

        $y = $yEnd + (40 * 2);

        /*Begin Instructions */
        if ($product->getMethod()) {
            $GlassTypeHeaderText = 'Preparation:';
            imagettftext($backgroundImage, $headerFontSize, 0, $glassX - 1, $y + 1, $grey, $robotoMedium, $GlassTypeHeaderText);
            imagettftext($backgroundImage, $headerFontSize, 0, $glassX, $y, $green, $robotoMedium, $GlassTypeHeaderText);
            $y += 60;

            $method = $product->getResource()->getAttribute('method')->getFrontend()->getValue($product);
//lets convert linebreaks to "/n"
            $methodConvertedBRstoNL = preg_replace('#<br\s*/?>#i', "\n", $method);
//Lets remove all HTML
            $methodWithoutHtml = strip_tags(html_entity_decode(str_replace("&nbsp;", " ", $methodConvertedBRstoNL))); //replace non-breaking spaces, decode html, and then strip the html
//Remove item numbers (1. 2. 3.)
            $methodWithoutNumbers = preg_replace('/\d./', '', $methodWithoutHtml);
//Let's remove excess whitespace and lines
            $method = preg_replace('/[ \t]+/', ' ', preg_replace('/\s*$^\s*/m', "\n", $methodWithoutNumbers));
//Convert text to ingredient array by new lines
            $methodArray = explode("\n", $method);
//Any array elements that are empty or have whitespace are trimmed
            $methodArray = array_map('trim', $methodArray);
//Now remove any empty array values
            $methodArray = array_diff($methodArray, array(''));
            /* $ingredients = [
                "Ice", "1 oz. Aimlessmedia Tiki Blend", "1 oz. dry gin", "1/2 oz orange liqueur", "1/2 oz. fresh lime juice", "2 oz. pineapple juice", "2 dashes bitters"
            ];*/

            foreach ($methodArray as $arrayKey => $instruction) {
                $linesOfInstructions[] = explode('|', wordwrap($instruction, 32, '|'));
            }

// Loop through the lines and place them on the image
            foreach ($linesOfInstructions as $order => $recipeInstruction) {
                $stepNum = $order + 1;
                $lastStep = null;
                foreach ($recipeInstruction as $recipeInstructionLine) {
                    if ($stepNum == $lastStep) {
                        $prependLine = "    ";
                    } else {
                        $prependLine = $stepNum . ". ";
                    }
                    $recipeInstructionLine = $prependLine . $recipeInstructionLine;
                    $x = 40;
                    imagettftext($backgroundImage, $fontSize, 0, $x - 1, $y + 1, $grey, $robotLight, $recipeInstructionLine);
                    imagettftext($backgroundImage, $fontSize, 0, $x, $y, $green, $robotoLight, $recipeInstructionLine);

                    // Increment Y so the next line is below the previous line
                    $y += $fontSize * 1.5;
                    $lastStep = $stepNum;
                }
            }

            if ($product->getGarnish()) {
                $xStart = $marge_left;
                $yStart = $y - 10; //pickup where our text left off
                $heightOfDivider = $yStart + 5;
                $xEnd = $newProductImage_Width; //we want it as wide as our image
                $yEnd = $heightOfDivider;
                imagefilledrectangle($backgroundImage, $xStart, $yStart, $xEnd, $yEnd, $green);

                $y = $yEnd + (40 * 2);

// Begin Garnish

                $GlassTypeHeaderText = 'Optional Garnish:';
                imagettftext($backgroundImage, $headerFontSize, 0, $glassX - 1, $y + 1, $grey, $robotoMedium, $GlassTypeHeaderText);
                imagettftext($backgroundImage, $headerFontSize, 0, $glassX, $y, $green, $robotoMedium, $GlassTypeHeaderText);
                $y += 60;
                $garnishes = explode(",", $product->getResource()
                    ->getAttribute('garnish')->getFrontend()
                    ->getValue($product));
                /* $garnishes = [
                     "Fresh pinapple wedge", "leaf Maraschino cherry", "fresh berries"
                 ];*/
                $implodedGarnishes = false;
                foreach ($garnishes as $garnish) {
                    if ($implodedGarnishes == false) {
                        $implodedGarnishes = $garnish;
                    } else {
                        $implodedGarnishes = $implodedGarnishes . ', ' . $garnish;
                    }
                }
                $search = ',';
                $replace = ' and';
                $cleanGarnishStr = str_replace('  ', " ", strrev(implode(strrev($replace), explode($search, strrev($implodedGarnishes), 2))) . ".");

// Loop through the lines and place them on the image
                $garnishLines = explode('|', wordwrap($cleanGarnishStr, 30, '|', 1));
                foreach ($garnishLines as $garnishLine) {
                    $x = 40;
                    imagettftext($backgroundImage, $fontSize, 0, $x - 1, $y + 1, $grey, $robotLight, $garnishLine);
                    imagettftext($backgroundImage, $fontSize, 0, $x, $y, $green, $robotoLight, $garnishLine);

                    // Increment Y so the next line is below the previous line
                    $y += 40;
                }
            }
        }
        $finalHeight = $y;
        /* Our original background image is extremely tall because we wanted to ensure that all of our content fit into it.
           Now we need to copy that image to one the size of our content
         */
        $finalImage = imagecreatetruecolor($bgWidth, $finalHeight);
        imagecopyresampled($finalImage, $backgroundImage, 0, 0, 0, 0, $bgWidth, $finalHeight, $bgWidth, $finalHeight);

// Output and free memory
        //       switch ($productImageType) {
//            case IMAGETYPE_PNG:
//               // header('Content-type: image/png');
//                $imgExt="png";
//                $imageName = time() . "." . $imgExt;
//                $temporarySavedImage = $tmpPath . $imageName;
//                imagepng($finalImage,$temporarySavedImage);
//                break;
//            case IMAGETYPE_JPEG:
        // header('Content-type: image/jpeg');
        $imgExt = "jpg";
        $imageName = time() . "." . $imgExt;
        $temporarySavedImage = $tmpPath . $imageName;
        imagejpeg($finalImage, $temporarySavedImage);
        //              break;
//            default :
        /**/;
        //     }

        $mode = array("pinterest_image");
        if (file_exists($temporarySavedImage)) {
            try {
                $product->addImageToMediaGallery($temporarySavedImage, $mode, true, true);
                $product->save();
            } catch (Exception $e) {
                Mage::getSingleton("core/session")->addError($e->getMessage());
                return false;
            }
        } else {
            $error = "Product does not have an image or the path is incorrect. Path was: {$temporarySavedImage}<br/>";
            Mage::getSingleton("core/session")->addError($error);
            return false;
        }
        imagedestroy($finalImage);
        imagedestroy($temporarySavedImage);
        imagedestroy($backgroundImage);
        return true;
    }
}