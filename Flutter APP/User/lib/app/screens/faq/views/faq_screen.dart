import 'package:flutter/material.dart';

import 'package:get/get.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:ovopay/app/components/shimmer/list_tile_shimmer.dart';
import 'package:ovopay/app/screens/faq/controller/faq_controller.dart';

import '../../../../core/data/repositories/faq_repo/faq_repo.dart';
import '../../../../core/utils/dimensions.dart';
import '../../../../core/utils/my_strings.dart';

import 'widget/faq_widget.dart';

class FaqScreen extends StatefulWidget {
  const FaqScreen({super.key});

  @override
  State<FaqScreen> createState() => _FaqScreenState();
}

class _FaqScreenState extends State<FaqScreen> {
  @override
  void initState() {
    Get.put(FaqRepo());
    final controller = Get.put(FaqController(faqRepo: Get.find()));
    super.initState();

    WidgetsBinding.instance.addPostFrameCallback((timeStamp) {
      controller.loadData();
    });
  }

  @override
  Widget build(BuildContext context) {
    return MyCustomScaffold(
      pageTitle: MyStrings.faq,
      body: GetBuilder<FaqController>(
        builder: (controller) => controller.isLoading
            ? const ListTileShimmer()
            : SingleChildScrollView(
                physics: const BouncingScrollPhysics(),
                child: ListView.separated(
                  shrinkWrap: true,
                  scrollDirection: Axis.vertical,
                  physics: const NeverScrollableScrollPhysics(),
                  itemCount: controller.faqList.length,
                  separatorBuilder: (context, index) => const SizedBox(height: Dimensions.space10),
                  itemBuilder: (context, index) => FaqListItem(
                    answer: (controller.faqList[index].dataValues?.answer ?? '').tr,
                    question: (controller.faqList[index].dataValues?.question ?? '').tr,
                    index: index,
                    press: () {
                      controller.changeSelectedIndex(index);
                    },
                    selectedIndex: controller.selectedIndex,
                  ),
                ),
              ),
      ),
    );
  }
}
