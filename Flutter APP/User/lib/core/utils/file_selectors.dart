import 'dart:io';
import 'package:file_picker/file_picker.dart';
import 'package:image_picker/image_picker.dart';
import 'package:ovopay/core/utils/util.dart';

class FileSelector {
  final ImagePicker _picker = ImagePicker();

  /// Select an image from the gallery
  Future<File?> selectImageFromGallery() async {
    try {
      final XFile? image = await _picker.pickImage(source: ImageSource.gallery);
      if (image != null) {
        return File(image.path);
      }
    } catch (e) {
      printE("Error picking image from gallery: $e");
    }
    return null;
  }

  /// Capture an image using the camera
  Future<File?> captureImageFromCamera() async {
    try {
      final XFile? image = await _picker.pickImage(source: ImageSource.camera);
      if (image != null) {
        return File(image.path);
      }
    } catch (e) {
      printE("Error capturing image with camera: $e");
    }
    return null;
  }

  /// Pick a document from the file system
  Future<File?> selectDocument() async {
    try {
      final FilePickerResult? result = await FilePicker.platform.pickFiles(
        type: FileType.custom,
        allowedExtensions: [
          'pdf',
          'doc',
          'docx',
          'txt',
        ], // Limit to document types
      );
      if (result != null) {
        return File(result.files.single.path!);
      }
    } catch (e) {
      printE("Error picking document: $e");
    }
    return null;
  }

  /// Pick any file (images or documents)
  Future<File?> selectAnyFile() async {
    try {
      final FilePickerResult? result = await FilePicker.platform.pickFiles();
      if (result != null) {
        return File(result.files.single.path!);
      }
    } catch (e) {
      printE("Error picking file: $e");
    }
    return null;
  }

  /// Select multiple images from the gallery
  Future<List<File>> selectMultipleImagesFromGallery() async {
    try {
      final List<XFile> images = await _picker.pickMultiImage();
      return images.map((xfile) => File(xfile.path)).toList();
    } catch (e) {
      printE("Error picking multiple images from gallery: $e");
    }
    return [];
  }

  /// Pick multiple files of any type
  Future<List<File>> selectMultipleFiles() async {
    try {
      final FilePickerResult? result = await FilePicker.platform.pickFiles(
        allowMultiple: true,
        type: FileType.custom,
        allowedExtensions: ['jpg', 'png', 'jpeg', 'pdf', 'doc', 'docx'],
      );
      if (result != null) {
        return result.paths.map((path) => File(path!)).toList();
      }
    } catch (e) {
      printE("Error picking multiple files: $e");
    }
    return [];
  }
}
