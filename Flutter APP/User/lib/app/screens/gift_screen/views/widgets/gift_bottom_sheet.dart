import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/bottom-sheet/bottom_sheet_bar.dart';
import 'package:ovopay/app/components/bottom-sheet/custom_bottom_sheet_plus.dart';
import 'package:ovopay/app/components/image/my_asset_widget.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/components/text/header_text.dart';
import 'package:ovopay/app/screens/gift_screen/controller/gift_controller.dart';
import 'package:ovopay/core/data/models/country_model/country_model.dart';
import 'package:ovopay/environment.dart';
import '../../../../../core/data/models/modules/gift_card/all_gift_card_response_model.dart';
import '../../../../../core/utils/util_exporter.dart';

class GiftBottomSheet {
  static void giftCountryBottomSheet(
    BuildContext context, {
    CountryData? selectedCountry,
    required void Function(CountryData data) onSelectedData,
  }) {
    GiftController countryController = Get.find<GiftController>();
    final TextEditingController searchController = TextEditingController();
    List<CountryData> filteredList = List<CountryData>.from(countryController.allCountryList);

    CustomBottomSheetPlus(
      isNeedAnimatedPadding: false,
      child: SafeArea(
        child: Builder(
          builder: (context) {
            return StatefulBuilder(
              builder: (BuildContext context, setState) {
                void filterList(String query) {
                  final q = query.trim().toLowerCase();
                  setState(() {
                    if (q.isEmpty) {
                      filteredList = List<CountryData>.from(countryController.allCountryList);
                    } else {
                      filteredList = countryController.allCountryList.where((country) {
                        final name = (country.name ?? "").toLowerCase();

                        return name.contains(q);
                      }).toList();
                    }
                  });
                }

                return Container(
                  height: MediaQuery.of(context).size.height * .82,
                  padding: EdgeInsets.only(
                    bottom: MediaQuery.of(context).viewInsets.bottom,
                  ),
                  decoration: BoxDecoration(
                    color: MyColor.getWhiteColor(),
                    borderRadius: const BorderRadius.only(
                      topLeft: Radius.circular(20),
                      topRight: Radius.circular(20),
                    ),
                  ),
                  child: Column(
                    children: [
                      const BottomSheetBar(),
                      spaceDown(Dimensions.space20),
                      Row(
                        children: [
                          Expanded(
                            child: HeaderText(
                              text: MyStrings.selectACountry,
                              textStyle: MyTextStyle.headerH3.copyWith(
                                color: MyColor.getHeaderTextColor(),
                              ),
                            ),
                          ),
                          IconButton(
                            onPressed: () {},
                            icon: IconButton(
                              padding: EdgeInsets.all(Dimensions.space3.w),
                              style: IconButton.styleFrom(),
                              onPressed: () {
                                Navigator.pop(context);
                              },
                              icon: MyAssetImageWidget(
                                color: MyColor.getHeaderTextColor(),
                                isSvg: true,
                                assetPath: MyIcons.closeButton,
                                width: Dimensions.space40.w,
                                height: Dimensions.space40.w,
                              ),
                            ),
                          ),
                        ],
                      ),
                      spaceDown(Dimensions.space20),
                      // 🔍 Search Field
                      TextField(
                        controller: searchController,
                        textInputAction: TextInputAction.search,
                        onChanged: (v) {
                          filterList(v);
                        },
                        onSubmitted: (_) => FocusScope.of(context).unfocus(),
                        decoration: InputDecoration(
                          hintText: MyStrings.searchACountry,
                          prefixIcon: const Icon(Icons.search),
                          filled: true,
                          fillColor: MyColor.getWhiteColor(),
                          contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                          enabledBorder: UnderlineInputBorder(
                            borderSide: BorderSide(color: MyColor.getPrimaryColor(), width: 1.5), // underline color
                          ),
                          focusedBorder: UnderlineInputBorder(
                            borderSide: BorderSide(color: MyColor.getPrimaryColor(), width: 2), // focused underline
                          ),
                        ),
                      ),

                      spaceDown(Dimensions.space10),
                      Flexible(
                        child: filteredList.isEmpty
                            ? FittedBox(
                                child: NoDataWidget(
                                text: MyStrings.noCountryFound,
                              ))
                            : ListView.builder(
                                padding: EdgeInsets.zero,
                                itemCount: filteredList.length,
                                itemBuilder: (context, index) {
                                  var countryItem = filteredList[index];
                                  return GestureDetector(
                                    onTap: () {
                                      onSelectedData(countryItem);
                                      Navigator.pop(context);
                                    },
                                    child: Container(
                                      padding: const EdgeInsets.symmetric(
                                        vertical: Dimensions.space15,
                                      ),
                                      margin: EdgeInsetsDirectional.only(
                                        end: Dimensions.space10,
                                      ),
                                      decoration: BoxDecoration(
                                        color: MyColor.transparentColor,
                                        border: Border(
                                          bottom: BorderSide(
                                            color: MyColor.getBorderColor(),
                                            width: 0.5,
                                          ),
                                        ),
                                      ),
                                      child: Row(
                                        children: [
                                          Expanded(
                                            child: Row(
                                              children: [
                                                Padding(
                                                  padding: const EdgeInsetsDirectional.only(
                                                    end: Dimensions.space10,
                                                  ),
                                                  child: MyNetworkImageWidget(
                                                    radius: Dimensions.radiusProMax.r,
                                                    imageUrl: UrlContainer.countryFlagImageLink.replaceAll(
                                                      "{countryCode}",
                                                      (countryItem.isoName ?? Environment.defaultCountryCode).toLowerCase(),
                                                    ),
                                                    height: Dimensions.space30.w,
                                                    width: Dimensions.space30.w,
                                                    boxFit: BoxFit.fitHeight,
                                                  ),
                                                ),
                                                Expanded(
                                                  child: Text(
                                                    countryItem.name ?? "",
                                                    style: MyTextStyle.sectionTitle3.copyWith(
                                                      color: MyColor.getHeaderTextColor(),
                                                    ),
                                                  ),
                                                ),
                                              ],
                                            ),
                                          ),
                                          Icon(
                                            selectedCountry?.id == countryItem.id ? Icons.check_circle_outlined : Icons.circle_outlined,
                                            color: selectedCountry?.id == countryItem.id ? MyColor.getPrimaryColor() : MyColor.getBlackColor().withValues(alpha: 0.35),
                                            weight: 0.5,
                                            size: Dimensions.space25.w,
                                          ),
                                        ],
                                      ),
                                    ),
                                  );
                                },
                              ),
                      ),
                    ],
                  ),
                );
              },
            );
          },
        ),
      ),
    ).show(context);
  }

  static void categoryBottomSheet(
    BuildContext context, {
    GiftCategory? selectedCategory,
    required void Function(GiftCategory data) onSelectedData,
  }) {
    GiftController giftController = Get.find<GiftController>();
    final TextEditingController searchController = TextEditingController();
    List<GiftCategory> filteredList = List<GiftCategory>.from(giftController.giftCategoryList);

    CustomBottomSheetPlus(
      child: SafeArea(
        child: Builder(
          builder: (context) {
            return StatefulBuilder(
              builder: (BuildContext context, setState) {
                void filterList(String query) {
                  final q = query.trim().toLowerCase();
                  setState(() {
                    if (q.isEmpty) {
                      filteredList = List<GiftCategory>.from(giftController.giftCategoryList);
                    } else {
                      filteredList = giftController.giftCategoryList.where((country) {
                        final name = (country.name ?? "").toLowerCase();
                        return name.contains(q);
                      }).toList();
                    }
                  });
                }

                return Container(
                  height: MediaQuery.of(context).size.height * .82,
                  padding: EdgeInsets.only(
                    bottom: MediaQuery.of(context).viewInsets.bottom,
                  ),
                  decoration: BoxDecoration(
                    color: MyColor.getWhiteColor(),
                    borderRadius: const BorderRadius.only(
                      topLeft: Radius.circular(20),
                      topRight: Radius.circular(20),
                    ),
                  ),
                  child: Column(
                    children: [
                      const BottomSheetBar(),
                      spaceDown(Dimensions.space20),
                      Row(
                        children: [
                          Expanded(
                            child: HeaderText(
                              text: MyStrings.selectACategory,
                              textStyle: MyTextStyle.headerH3.copyWith(
                                color: MyColor.getHeaderTextColor(),
                              ),
                            ),
                          ),
                          IconButton(
                            onPressed: () {},
                            icon: IconButton(
                              padding: EdgeInsets.all(Dimensions.space3.w),
                              style: IconButton.styleFrom(),
                              onPressed: () {
                                Navigator.pop(context);
                              },
                              icon: MyAssetImageWidget(
                                color: MyColor.getHeaderTextColor(),
                                isSvg: true,
                                assetPath: MyIcons.closeButton,
                                width: Dimensions.space40.w,
                                height: Dimensions.space40.w,
                              ),
                            ),
                          ),
                        ],
                      ),
                      spaceDown(Dimensions.space20),
                      // 🔍 Search Field
                      TextField(
                        controller: searchController,
                        textInputAction: TextInputAction.search,
                        onChanged: (v) {
                          filterList(v);
                        },
                        onSubmitted: (_) => FocusScope.of(context).unfocus(),
                        decoration: InputDecoration(
                          hintText: MyStrings.searchACategory,
                          prefixIcon: const Icon(Icons.search),
                          filled: true,
                          fillColor: MyColor.getWhiteColor(),
                          contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                          enabledBorder: UnderlineInputBorder(
                            borderSide: BorderSide(color: MyColor.getPrimaryColor(), width: 1.5), // underline color
                          ),
                          focusedBorder: UnderlineInputBorder(
                            borderSide: BorderSide(color: MyColor.getPrimaryColor(), width: 2), // focused underline
                          ),
                        ),
                      ),

                      spaceDown(Dimensions.space10),
                      Flexible(
                        child: filteredList.isEmpty
                            ? FittedBox(child: NoDataWidget())
                            : ListView.builder(
                                padding: EdgeInsets.zero,
                                itemCount: filteredList.length,
                                itemBuilder: (context, index) {
                                  var categoryItem = filteredList[index];

                                  return GestureDetector(
                                    onTap: () {
                                      onSelectedData(categoryItem);
                                      Navigator.pop(context);
                                    },
                                    child: Container(
                                      padding: const EdgeInsets.symmetric(
                                        vertical: Dimensions.space15,
                                      ),
                                      margin: EdgeInsetsDirectional.only(
                                        end: Dimensions.space10,
                                      ),
                                      decoration: BoxDecoration(
                                        color: MyColor.transparentColor,
                                        border: Border(
                                          bottom: BorderSide(
                                            color: MyColor.getBorderColor(),
                                            width: 0.5,
                                          ),
                                        ),
                                      ),
                                      child: Row(
                                        children: [
                                          Expanded(
                                            child: Row(
                                              children: [
                                                Expanded(
                                                  child: Text(
                                                    categoryItem.name ?? "",
                                                    style: MyTextStyle.sectionTitle3.copyWith(
                                                      color: MyColor.getHeaderTextColor(),
                                                    ),
                                                  ),
                                                ),
                                              ],
                                            ),
                                          ),
                                          Icon(
                                            selectedCategory?.id == categoryItem.id ? Icons.check_circle_outlined : Icons.circle_outlined,
                                            color: selectedCategory?.id == categoryItem.id ? MyColor.getPrimaryColor() : MyColor.getBlackColor().withValues(alpha: 0.35),
                                            weight: 0.5,
                                            size: Dimensions.space25.w,
                                          ),
                                        ],
                                      ),
                                    ),
                                  );
                                },
                              ),
                      ),
                    ],
                  ),
                );
              },
            );
          },
        ),
      ),
    ).show(context);
  }
}
