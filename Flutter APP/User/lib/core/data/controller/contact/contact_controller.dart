// ignore_for_file: unnecessary_null_comparison

import 'package:fast_contacts/fast_contacts.dart';
import 'package:get/get.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';
import 'package:permission_handler/permission_handler.dart';

import '../../../utils/util_exporter.dart';

// Attention: use this package https://pub.dev/packages/fast_contacts
class ContactController extends GetxController {
  List<Contact> contacts = [];
  List<Contact> filterContact = [];
  int selectedExpandIndex = -1;
  bool isLoading = false;
  bool isPermissionGranted = false;
  bool isSearching = false;

  // Singleton instance for global use
  static ContactController get to => Get.find<ContactController>();

  // @override
  // void onInit() {
  //   super.onInit();
  //   requestPermissions();
  // }

  Future<void> requestPermissions() async {
    final status = await Permission.contacts.request();
    if (status.isGranted || status.isLimited) {
      isPermissionGranted = true;
      getContact();
    } else {
      isPermissionGranted = false;
      update();
    }
  }

  Future<void> getContact() async {
    selectedExpandIndex = -1;
    contacts.clear();
    filterContact.clear();
    isLoading = true;
    update();
    if (isPermissionGranted) {
      try {
        final list = await FastContacts.getAllContacts(
          fields: [ContactField.displayName, ContactField.phoneNumbers],
        );
        var data = list.where((v) {
          return v.phones.any(
                (phone) =>
                    phone.number
                        .toFormattedPhoneNumber(
                          digitsFromEnd: SharedPreferenceService.getMaxMobileNumberDigit(),
                        )
                        .length >=
                    SharedPreferenceService.getMaxMobileNumberDigit(),
              ) &&
              v.displayName != null;
        }).toList();
        contacts.addAll(data);
        filterContact.addAll(data);
        filterContact.sort(
          (a, b) => a.displayName.compareTo(b.displayName),
        ); // Sort after fetching
      } catch (e) {
        printE("Error fetching contacts: $e");
      }
    }
    isLoading = false;
    update();
  }

  void searchContact(String val) {
    filterContact = contacts
        .where(
          (contact) => contact.displayName.toLowerCase().contains(val.toLowerCase()),
        )
        .toList();
    update();
  }

  void filterContacts(String query) {
    isSearching = true;
    update();

    // Trim the query to remove unnecessary spaces
    query = query.trim();

    if (query.isEmpty) {
      // If the query is empty, reset the filtered contacts
      filterContact = List.from(contacts);
    } else {
      // Filter by displayName and phone numbers
      filterContact = contacts.where((contact) {
        final name = contact.displayName.toLowerCase();
        final phones = contact.phones.map((phone) => phone.number.toLowerCase()).toList();

        // Check if the query matches either the name or any phone number
        return name.contains(query.toLowerCase()) || phones.any((phone) => phone.contains(query.toLowerCase()));
      }).toList();
    }

    isSearching = false;
    update();
  }

  void toggleSelectedExpandIndex(int value) {
    if (selectedExpandIndex == value) {
      selectedExpandIndex = -1;
    } else {
      selectedExpandIndex = value;
    }
    update();
  }
}
