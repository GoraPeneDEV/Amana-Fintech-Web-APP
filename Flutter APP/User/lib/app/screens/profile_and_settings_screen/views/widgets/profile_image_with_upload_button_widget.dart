import 'dart:io';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/core/utils/util_exporter.dart';
import 'package:image_picker/image_picker.dart';
import 'package:skeletonizer/skeletonizer.dart';
import 'custom_image_cropper_widget.dart';

class ProfileImageWithUploadButtonWidget extends StatefulWidget {
  const ProfileImageWithUploadButtonWidget({
    super.key,
    required this.imageUrl,
    this.showUploadIcon = true,
    this.onTap,
    this.onChanged,
    required this.imageAlt,
  });
  final String imageUrl;
  final String imageAlt;
  final bool showUploadIcon;
  final VoidCallback? onTap;
  final Function(File)? onChanged;

  @override
  State<ProfileImageWithUploadButtonWidget> createState() => _ProfileImageWithUploadButtonWidgetState();
}

class _ProfileImageWithUploadButtonWidgetState extends State<ProfileImageWithUploadButtonWidget> {
  final ImagePicker _picker = ImagePicker();
  XFile? _imageFile;

  // Function to pick image from gallery
  Future<void> _pickImageFromGallery() async {
    final pickedFile = await _picker.pickImage(
      source: ImageSource.gallery,
      imageQuality: 80,
    );

    if (pickedFile != null) {
      final bytes = await pickedFile.readAsBytes();

      // Use custom cropper popup
      if (!mounted) return;
      await CustomImageCropper.showCropDialog(
        context: context,
        imageBytes: bytes,
        onCropped: (croppedFile) {
          if (!mounted) return;
          setState(() {
            _imageFile = XFile(croppedFile.path);
            widget.onChanged!(croppedFile);
          });
          // Navigator.of(context).pop(); // Close the dialog
        },
      );
    }
  }

  // Function to pick image from camera
  Future<void> _pickImageFromCamera() async {
    final pickedFile = await _picker.pickImage(
      source: ImageSource.camera,
      imageQuality: 80,
    );

    if (pickedFile != null) {
      final bytes = await pickedFile.readAsBytes();

      // Use custom cropper popup
      if (!mounted) return;
      await CustomImageCropper.showCropDialog(
        context: context,
        imageBytes: bytes,
        onCropped: (croppedFile) {
          if (!mounted) return;
          setState(() {
            _imageFile = XFile(croppedFile.path);
            widget.onChanged!(croppedFile);
          });
          // Navigator.of(context).pop(); // Close the dialog
        },
      );
    }
  }

  // Function to show the Cupertino action sheet for image source selection
  void _showImageSourceActionSheet() {
    showCupertinoModalPopup(
      context: context,
      builder: (BuildContext context) {
        return CupertinoActionSheet(
          title: Text(
            MyStrings.chooseAnOption.tr,
            style: MyTextStyle.sectionTitle.copyWith(
              color: MyColor.getHeaderTextColor(),
            ),
          ),
          actions: [
            CupertinoActionSheetAction(
              onPressed: () {
                Navigator.pop(context);
                _pickImageFromGallery();
              },
              child: Text(
                MyStrings.gallery.tr,
                style: MyTextStyle.sectionTitle3.copyWith(
                  color: MyColor.getBodyTextColor(),
                ),
              ),
            ),
            CupertinoActionSheetAction(
              onPressed: () {
                Navigator.pop(context);
                _pickImageFromCamera();
              },
              child: Text(
                MyStrings.camera.tr,
                style: MyTextStyle.sectionTitle3.copyWith(
                  color: MyColor.getBodyTextColor(),
                ),
              ),
            ),
          ],
          cancelButton: CupertinoActionSheetAction(
            onPressed: () {
              Navigator.pop(context);
            },
            child: Text(
              MyStrings.cancel.tr,
              style: MyTextStyle.sectionTitle3.copyWith(
                color: MyColor.getBodyTextColor(),
              ),
            ),
          ),
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: widget.showUploadIcon == true
          ? () {
              if (widget.onTap == null) {
                _showImageSourceActionSheet();
              } else {
                widget.onTap!();
              }
            }
          : null,
      child: Stack(
        children: [
          Container(
            decoration: BoxDecoration(
              border: Border.all(
                color: MyColor.getPrimaryColor().withValues(alpha: 0.15),
                width: Dimensions.space4.w,
              ),
              borderRadius: BorderRadius.circular(Dimensions.radiusProMax.r),
            ),
            child: Container(
              decoration: BoxDecoration(
                border: Border.all(
                  color: MyColor.getPrimaryColor(),
                  width: 1.w,
                ),
                borderRadius: BorderRadius.circular(Dimensions.radiusProMax.r),
              ),
              child: Container(
                decoration: BoxDecoration(
                  border: Border.all(
                    color: MyColor.getPrimaryColor().withValues(alpha: 0.15),
                    width: Dimensions.space3.w,
                  ),
                  borderRadius: BorderRadius.circular(
                    Dimensions.radiusProMax.r,
                  ),
                ),
                child: _imageFile == null
                    ? Skeleton.replace(
                        replace: true,
                        replacement: Bone.circle(size: Dimensions.space80.w),
                        child: MyNetworkImageWidget(
                          imageUrl: widget.imageUrl,
                          isProfile: true,
                          imageAlt: widget.imageAlt,
                          radius: Dimensions.radiusMax.r,
                          width: Dimensions.space80.w,
                          height: Dimensions.space80.w,
                          boxFit: BoxFit.cover,
                        ),
                      )
                    : MyAssetImageWidget(
                        assetPath: _imageFile?.path ?? '',
                        isFile: true,
                        radius: Dimensions.radiusMax.r,
                        boxFit: BoxFit.cover,
                        width: Dimensions.space80.w,
                        height: Dimensions.space80.w,
                      ),
              ),
            ),
          ),
          if (widget.showUploadIcon)
            PositionedDirectional(
              bottom: Dimensions.space2,
              end: Dimensions.space2,
              child: Container(
                decoration: BoxDecoration(
                  color: MyColor.getWhiteColor(), // or any background
                  borderRadius: BorderRadius.circular(Dimensions.radiusMax.r),
                  boxShadow: [
                    BoxShadow(
                      color: MyColor.getPrimaryColor().withValues(alpha: 0.3),
                      blurRadius: 5,
                      offset: Offset(-3, -8),
                    ),
                  ],
                ),
                width: Dimensions.space30.w,
                height: Dimensions.space30.w,
                padding: EdgeInsetsDirectional.all(Dimensions.space5),
                child: MyAssetImageWidget(
                  color: MyColor.getPrimaryColor(),
                  isSvg: true,
                  assetPath: MyIcons.uploadIcon,
                  width: Dimensions.space24.w,
                  height: Dimensions.space24.w,
                ),
              ),
            ),
        ],
      ),
    );
  }
}
