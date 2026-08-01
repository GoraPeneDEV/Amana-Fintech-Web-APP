import 'package:flutter/material.dart';
import 'package:flutter_widget_from_html/flutter_widget_from_html.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/custom_loader/custom_loader.dart';
import 'package:ovopay/app/components/dialog/exit_dialog.dart';
import 'package:ovopay/app/components/image/my_network_image_widget.dart';
import 'package:ovopay/app/components/will_pop_widget.dart';
import 'package:ovopay/core/data/services/service_exporter.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class MaintenanceContentScreen extends StatelessWidget {
  const MaintenanceContentScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return WillPopWidget(
      child: MyCustomScaffold(
        pageTitle: MyStrings.maintenanceMode.tr,
        onBackButtonTap: () {
          showExitDialog(context);
        },
        body: CustomAppCard(
          child: SingleChildScrollView(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Center(
                  child: MyNetworkImageWidget(
                    imageUrl: "${UrlContainer.maintenanceImagePath}${SharedPreferenceService.maintenanceModContentContent()?.dataValues?.image}",
                    width: Dimensions.space100 * 2,
                    height: Dimensions.space100 * 2,
                    boxFit: BoxFit.contain,
                  ),
                ),
                Center(
                  child: HtmlWidget(
                    "${SharedPreferenceService.maintenanceModContentContent()?.dataValues?.description}",
                    textStyle: MyTextStyle.bodyTextStyle1.copyWith(
                      color: MyColor.getBodyTextColor(),
                    ),
                    onLoadingBuilder: (context, element, loadingProgress) => const Center(child: CustomLoader()),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
