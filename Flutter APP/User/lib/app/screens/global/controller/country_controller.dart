import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/core/data/models/country_model/country_model.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';
import 'package:ovopay/environment.dart';

import '../../../../core/utils/util_exporter.dart';

class CountryController {
  // Country list and filtered list
  String selectedCountry = "";
  CountryData? selectedCountryData;
  List<CountryData> countryList = [];
  List<CountryData> filteredCountries = [];

  // Text controller for search
  TextEditingController searchController = TextEditingController();

  // Initialize with an empty country list or populate it here.
  void initialize() {
    try {
      CountryModel countryModel = SharedPreferenceService.getCountryJsonDataData();
      selectedCountry = countryModel.data?.selectedCountryCode ?? Environment.defaultCountryCode;
      selectedCountryData = countryModel.data?.countries?.firstWhereOrNull(
        (country) => country.code?.toLowerCase() == selectedCountry.toLowerCase(),
      );
      countryList = countryModel.data?.countries ?? [];
      filteredCountries = List.from(countryList);
      if (SharedPreferenceService.getSelectedOperatingCountry().id != null) {
        selectedCountryData = countryList.firstWhereOrNull(
          (country) => country.code?.toLowerCase() == SharedPreferenceService.getSelectedOperatingCountry().code?.toLowerCase(),
        );
      }
    } catch (e) {
      printE(e.toString());
    }
  }

  // Filter countries based on search query
  void filterCountries(String query) {
    final q = query.trim().toLowerCase();
    if (query.isEmpty) {
      filteredCountries = List.from(countryList);
    } else {
      filteredCountries = countryList.where((country) {
        final name = (country.name ?? "").toLowerCase();
        final code = (country.dialCode ?? "").toLowerCase();
        return name.contains(q) || code.contains(q);
      }).toList();
    }
  }
}
