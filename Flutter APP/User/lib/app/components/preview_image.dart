import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:ovopay/app/components/card/my_custom_scaffold.dart';
import 'package:flutter_spinkit/flutter_spinkit.dart';
import 'package:ovopay/core/utils/util_exporter.dart';

class PreviewImage extends StatefulWidget {
  final String url;
  const PreviewImage({super.key, required this.url});

  @override
  State<PreviewImage> createState() => _PreviewImageState();
}

class _PreviewImageState extends State<PreviewImage> {
  String url = "";
  @override
  void initState() {
    url = widget.url;
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return MyCustomScaffold(
      pageTitle: "",
      body: InteractiveViewer(
        child: CachedNetworkImage(
          imageUrl: widget.url.toString(),
          imageBuilder: (context, imageProvider) => Container(
            decoration: BoxDecoration(
              boxShadow: const [],
              // borderRadius:  BorderRadius.circular(radius),
              image: DecorationImage(
                image: imageProvider,
                fit: BoxFit.contain,
              ),
            ),
          ),
          placeholder: (context, url) => SizedBox(
            child: ClipRRect(
              borderRadius: BorderRadius.circular(
                Dimensions.mediumRadius.r,
              ),
              child: Center(
                child: SpinKitFadingCube(
                  color: MyColor.getPrimaryColor().withValues(alpha: 0.3),
                  size: Dimensions.space20,
                ),
              ),
            ),
          ),
          errorWidget: (context, url, error) => SizedBox(
            child: ClipRRect(
              borderRadius: BorderRadius.circular(
                Dimensions.mediumRadius.r,
              ),
              child: Center(
                child: Icon(Icons.image, color: MyColor.getBodyTextColor()),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
