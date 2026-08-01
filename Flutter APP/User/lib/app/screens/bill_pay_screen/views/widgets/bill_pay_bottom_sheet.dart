import 'package:flutter/material.dart';
import 'package:ovopay/app/components/bottom-sheet/bottom_sheet_bar.dart';
import 'package:ovopay/app/components/bottom-sheet/custom_bottom_sheet_plus.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/screens/bill_pay_screen/controller/bill_pay_controller.dart';
import 'package:ovopay/core/data/models/modules/airtime_recharge/airtime_recharge_response_model.dart';

import '../../../../../core/utils/util_exporter.dart';

class BillPayBottomSheet {
  static void countryBottomSheet(
    BuildContext context, {
    CountryListModel? selectedCountry,
    required void Function(CountryListModel data) onSelectedData,
    required BillPayController controller,
  }) {
    final TextEditingController searchController = TextEditingController();
    List<CountryListModel> filteredList = List<CountryListModel>.from(controller.countryDataList);

    CustomBottomSheetPlus(
      isNeedAnimatedPadding: false,
      child: StatefulBuilder(
        builder: (BuildContext context, void Function(void Function()) setState) {
          void filterList(String query) {
            final q = query.trim().toLowerCase();
            setState(() {
              if (q.isEmpty) {
                filteredList = List<CountryListModel>.from(controller.countryDataList);
              } else {
                filteredList = controller.countryDataList.where((country) {
                  final name = (country.name ?? "").toLowerCase();
                  final code = (country.callingCodes != null && country.callingCodes!.isNotEmpty) ? country.callingCodes!.first.toLowerCase() : "";
                  return name.contains(q) || code.contains(q);
                }).toList();
              }
            });
          }

          return GestureDetector(
            behavior: HitTestBehavior.opaque,
            onTap: () {
              FocusScope.of(context).unfocus(); // unfocus keyboard
            },
            child: Container(
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
                  spaceDown(Dimensions.space16),

                  // 🔍 Search Field
                  TextField(
                    controller: searchController,
                    textInputAction: TextInputAction.search,
                    onChanged: filterList,
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

                  spaceDown(Dimensions.space16),

                  Flexible(
                    child: (filteredList.isEmpty)
                        ? FittedBox(
                            child: NoDataWidget(
                            text: MyStrings.noCountryFound,
                          ))
                        : ListView.builder(
                            padding: EdgeInsets.zero,
                            itemCount: filteredList.length,
                            itemBuilder: (context, index) {
                              final countryItem = filteredList[index];
                              return GestureDetector(
                                onTap: () {
                                  FocusScope.of(context).unfocus();
                                  onSelectedData(countryItem);
                                  Navigator.pop(context);
                                },
                                child: Container(
                                  padding: const EdgeInsets.all(Dimensions.space15),
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
                                      Padding(
                                        padding: const EdgeInsetsDirectional.only(end: Dimensions.space10),
                                        child: MyNetworkImageWidget(
                                          isSvg: true,
                                          imageUrl: countryItem.flagUrl ?? "",
                                          height: null,
                                          width: Dimensions.space45.w,
                                        ),
                                      ),
                                      Padding(
                                        padding: const EdgeInsetsDirectional.only(end: Dimensions.space10),
                                        child: Text(
                                          countryItem.callingCodes != null && countryItem.callingCodes!.isNotEmpty ? countryItem.callingCodes!.first : "",
                                          style: MyTextStyle.sectionTitle3.copyWith(
                                            color: MyColor.getHeaderTextColor(),
                                          ),
                                        ),
                                      ),
                                      Expanded(
                                        child: Text(
                                          '${countryItem.name}',
                                          style: MyTextStyle.sectionTitle3.copyWith(
                                            color: MyColor.getHeaderTextColor(),
                                          ),
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
            ),
          );
        },
      ),
    ).show(context);
  }
}
