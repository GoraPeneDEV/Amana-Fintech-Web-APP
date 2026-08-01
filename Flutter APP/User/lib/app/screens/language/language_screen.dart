import 'package:flutter/material.dart';

import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/custom_list_tile_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/custom_loader/custom_loader.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/no_data.dart';
import 'package:ovopay/app/screens/language/controller/my_language_controller.dart';
import 'package:ovopay/core/data/controller/localization/localization_controller.dart';
import 'package:ovopay/core/data/repositories/auth/general_setting_repo.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';

import '../../../core/utils/util_exporter.dart';

class LanguageScreen extends StatefulWidget {
  const LanguageScreen({super.key});

  @override
  State<LanguageScreen> createState() => _LanguageScreenState();
}

class _LanguageScreenState extends State<LanguageScreen> {
  String comeFrom = '';

  @override
  void initState() {
    Get.put(GeneralSettingRepo());
    Get.put(LocalizationController());
    final controller = Get.put(MyLanguageController(repo: Get.find()));

    comeFrom = Get.arguments ?? '';

    super.initState();

    WidgetsBinding.instance.addPostFrameCallback((timeStamp) {
      controller.loadLanguage();
    });
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<MyLanguageController>(
      builder: (controller) => MyCustomScaffold(
        pageTitle: MyStrings.language,
        body: controller.isLoading
            ? const CustomLoader()
            : controller.langList.isEmpty
                ? NoDataWidget()
                : SingleChildScrollView(
                    clipBehavior: Clip.none,
                    child: CustomAppCard(
                      padding: EdgeInsets.symmetric(
                        horizontal: Dimensions.space16.w,
                      ),
                      child: ListView.builder(
                        shrinkWrap: true,
                        addAutomaticKeepAlives: true,
                        padding: EdgeInsets.zero,
                        scrollDirection: Axis.vertical,
                        itemCount: controller.langList.length,
                        physics: const NeverScrollableScrollPhysics(),
                        itemBuilder: (context, index) {
                          var item = controller.langList[index];
                          return _buildMenuListTile(
                            controller: controller,
                            leading: MyNetworkImageWidget(
                              imageUrl: "${UrlContainer.languageImagePath}${item.imageUrl ?? ""}",
                              // isProfile: true,
                              width: 22.w,
                              height: 16.h,
                              boxFit: BoxFit.contain,
                            ),
                            title: item.languageName,
                            isSelected: item.languageCode ==
                                SharedPreferenceService.getString(
                                  SharedPreferenceService.languageCode,
                                  defaultValue: "en",
                                ).toLowerCase(),
                            onPressed: () => controller.changeLanguage(index),
                            trailing: controller.isChangeLangLoadingIndex == index
                                ? SizedBox(
                                    width: Dimensions.space25,
                                    height: Dimensions.space25,
                                    child: CircularProgressIndicator(
                                      strokeWidth: 1,
                                      color: MyColor.getPrimaryColor(),
                                    ),
                                  )
                                : null,
                            showBorder: controller.langList.length - 1 != (index),
                          );
                        },
                      ),
                    ),
                  ),
      ),
    );
  }

  Widget _buildMenuListTile({
    required String title,
    required VoidCallback onPressed,
    Widget? trailing,
    Widget? leading,
    bool showBorder = true,
    bool isSelected = false,
    required MyLanguageController controller,
  }) {
    return CustomListTileCard(
      showLeading: true,
      showBorder: showBorder,
      padding: EdgeInsets.symmetric(vertical: Dimensions.space17),
      title: title.tr,
      titleStyle: MyTextStyle.sectionTitle2.copyWith(
        color: MyColor.getHeaderTextColor(),
      ),
      leading: leading,
      trailing: trailing ??
          Icon(
            isSelected ? Icons.check : Icons.arrow_forward_ios_rounded,
            color: isSelected ? MyColor.getPrimaryColor() : MyColor.getBodyTextColor().withValues(alpha: 0.5),
            size: Dimensions.space16.w,
          ),
      onPressed: onPressed,
    );
  }
}
