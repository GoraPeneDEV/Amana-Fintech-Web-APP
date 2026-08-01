import 'package:flutter/material.dart';
import 'package:flutter_widget_from_html/flutter_widget_from_html.dart';
import 'package:get/get.dart';
import 'package:ovopay/app/components/buttons/category_button.dart';
import 'package:ovopay/app/components/card/custom_card.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/custom_loader/custom_loader.dart';
import 'package:ovopay/app/components/shimmer/privacy_policy_shimmer.dart';
import 'package:ovopay/app/screens/page_content_screen/controller/page_content_controller.dart';
import 'package:ovopay/core/data/repositories/privacy_repo/privacy_repo.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class PageContentScreen extends StatefulWidget {
  const PageContentScreen({super.key});

  @override
  State<PageContentScreen> createState() => _PageContentScreenState();
}

class _PageContentScreenState extends State<PageContentScreen> {
  @override
  void initState() {
    Get.put(PrivacyRepo());
    final controller = Get.put(PageContentController(repo: Get.find()));

    super.initState();

    WidgetsBinding.instance.addPostFrameCallback((timeStamp) {
      controller.loadData();
    });
  }

  String pageTitle = MyStrings.privacyPolicy;
  @override
  Widget build(BuildContext context) {
    return MyCustomScaffold(
      pageTitle: pageTitle.tr,
      body: GetBuilder<PageContentController>(
        builder: (controller) => SizedBox(
          width: MediaQuery.of(context).size.width,
          child: controller.isLoading
              ? const PrivacyPolicyShimmer()
              : CustomAppCard(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      SizedBox(
                        child: SingleChildScrollView(
                          scrollDirection: Axis.horizontal,
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.start,
                            children: List.generate(
                              controller.list.length,
                              (index) => Row(
                                children: [
                                  CategoryButton(
                                    color: controller.selectedIndex == index ? MyColor.getPrimaryColor() : MyColor.getBodyTextColor().withValues(alpha: .2),
                                    horizontalPadding: 8,
                                    verticalPadding: 7,
                                    textColor: controller.selectedIndex == index ? MyColor.getWhiteColor() : MyColor.getBlackColor(),
                                    text: controller.list[index].dataValues?.title ?? '',
                                    press: () {
                                      controller.changeIndex(index);
                                      setState(() {
                                        pageTitle = controller.list[index].dataValues?.title ?? '';
                                      });
                                    },
                                  ),
                                  const SizedBox(
                                    width: Dimensions.space10,
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: Dimensions.space15),
                      Expanded(
                        child: Center(
                          child: SingleChildScrollView(
                            child: HtmlWidget(
                              controller.selectedHtml,
                              textStyle: MyTextStyle.bodyTextStyle1.copyWith(
                                color: MyColor.getBodyTextColor(),
                              ),
                              onLoadingBuilder: (context, element, loadingProgress) => const Center(child: CustomLoader()),
                            ),
                          ),
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
