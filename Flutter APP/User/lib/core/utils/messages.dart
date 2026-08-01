import 'package:get/get.dart';

class LanguageMessages extends Translations {
  final Map<String, Map<String, String>> languages;

  LanguageMessages({required this.languages});

  @override
  Map<String, Map<String, String>> get keys {
    return languages;
  }
}
