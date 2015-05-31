---
layout: default
title: Dynamic Autotuning of Algorithmic Skeletons
---

(This post is based on a talk I gave for an internal conference at the
University of Edinburgh as part of my Msc in
[Pervasive Paralleism](http://pervasiveparallelism.inf.ed.ac.uk/).)

This post is about dynamic autotuning of Algorithmic Skeletons, or
rather, how we can achieve portable performance from libraries which
offer high levels of abstraction for parallel programming.

Let's start with an example. I want to compute the Mandelbrot set.

![Mandelbrot set](/images/2015-05-29-mandelbrot.png)

The easy way to do this is to write a short C program (as I did) which
will sequentially calculate the value of each pixel and write the
results out to a file. This is convenient, it's around 50 lines of
code, but it's *slow*.

So in the name of performance let's get the GPU involved and use
OpenCL. *All* we have to do is add a few headers, select a platform,
select a device, create a command queue, compile a program, create a
kernel, create a buffer, enqueue a kernel, read the buffer, handle any
errors... and we're good. Now we're looking at around 200 lines of
code, but, it's 20 times faster. In the age of multicores we're going
to *need* that extra performance, but as developers we're not willing
to pay the high price that this demands.

Algorithmic Skeletons offer a solution; by abstracting common patterns
of communication, they allow libraries and language authors to provide
robust parallel implementations of patterns which enables great ease
of use. I think that this point is best illustrated with some
gratuitous clipart of a cartoon skeleton doing a jig.

![Algorithmic Skeletons](/images/2015-05-29-skel.png)

If we implement our Mandelbrot program using Algorithmic Skeletons we
get a line count close to the sequential version, and with performance
close to the OpenCL version.

![Runtimes and Lines of Code](/images/2015-05-29-mandelbrot-loc-runtime.png)

While this is good, I think we can do better. There's clearly a
difference in performance between the OpenCL version and the skeleton
version, so in this post I'm going to argue that if we want both ease
of use *and* high performance, we need **autotuning**.

The purpose of my research project is to demonstrate dynamic
autotuning of algorithmic skeletons. For this I'm using SkelCL, which
offers OpenCL implementations of data-parallel skeletons, and I'm
going to be looking in particular at Stencil skeletons.

Stencils are patterns of computation which operate on uniform grids of
data, where the value of each cell is updated based on its current
value and the value of one or more neighbouring elements, which we'll
call the border region. In SkelCL, users provide a function to update
a cell's value, and SkelCL orchestrates this for execution on CPUs and
multiple GPUs. Each cell maps to a single work item; and this
collection of work items is then divided into **workgroups** for
execution on hardware threads.

![Decomposition of work items into workgroups](/images/2015-05-29-wg.png)

While the user is clearly in control of the type of work which is
executed, the size of the grid, and the size of the border region, it
is very much within the remit of the skeleton implementation to select
what workgroup size to use. I was interested in investigating just
what impact selecting a particular workgroup size can have on the
performance of stencils.

I designed an experiment to explore the optimisation space for
workgroup sizes with Stencil skeletons. I selected 14 synthetic
benchmarks representative of typical stencil applications in addition
to 3 real world applications taken from
[image processing](http://en.wikipedia.org/wiki/Canny_edge_detector),
[cellular automata](http://en.wikipedia.org/wiki/Conway%27s_Game_of_Life),
and a
[PDE solver](http://en.wikipedia.org/wiki/Finite-difference_time-domain_method).
A selection of different dataset sizes and data types were then used
to collect runtime data from 10 different combinations of CPUs, GPUs,
and multi-GPUs setups.

By collecting multiple runs of a fixed program/hardware/dataset
combination but using different workgroup sizes, I was able to perform
relative performance comparisons to see what the best workgroup size
for that combination is. By trying a bunch of different optimisations
and plotting the density of optimal values across the parameter space,
I can start to get a feel for the optimisation space.

The results are astounding.

![Distribution of optimal workgroup sizes](/images/2015-05-29-heatmap.png)

There is clearly no silver bullet value which works well for all
programs, devices, and datasets. Furthermore, the values which *are*
optimal are distributed wildly across the parameter space. By sorting
the workgroup sizes by the frequency at which they were optimal, we
can see that by using a fixed workgroup size, you will be optimal only
*10%* of the time. In fact, you need to select from 10 different
workgroup sizes just to be optimal 50% of the time.

![](/images/2015-05-29-best-wg.png)

In addition, the parameter space has *hard constraints*. Each OpenCL
device imposes a
[maximum workgroup size](https://www.khronos.org/registry/cl/sdk/1.0/docs/man/xhtml/clGetDeviceInfo.html)
which can be checked statically. More troubling, each kernel too
imposes a
[maximum workgroup size](https://www.khronos.org/registry/cl/sdk/1.1/docs/man/xhtml/clGetKernelWorkGroupInfo.html),
which can only be checked at runtime once a program has been compiled.

By applying these constraint tests, we can cull the list of possible
workgroup sizes to generate an autotuner with a ZeroR classifier,
i.e. a simple classifier that simply selects the workgroup size which
provides the highest average case performance and is legal for all
cases. We can now compare speedup of *all* tested workgroup sizes
relative to this ZeroR autotuner.

![Distributino of speedups across programs](/images/2015-05-29-violion-prog.png)

Hmm. It seems that there is a lot of room for improvement. This shows
the problem with having to generalise for all cases - you lose out on
up to *10x* performance improvements. Putting this all together, this
presents a compelling case for the development of an autotuner which
can select the optimal workgroup size at runtime, and that is what I
have set out to achieve.

The first step to developing the autotuner was feature
extraction. That means mining the dataset of experimental results to
begin to correlate **explanatory** variables with the measured
**dependent** variable (in this case, some measure of
performance). There are three sets of features we are interested in,
which will explain the hardware, software, and dataset.

For hardware features, it's simply a case of recording the number of
execution devices, and querying the OpenCL API to fetch relevant
device information, such as the number of cores available, and size of
local memory. Simple enough, moving on.

The software features are a little more tricky. We're looking for a
way to express the *computation* that a given source code
describes. For this, I first compile the kernels to LLVM bitcode, and
then use the static instruction counts generated by LLVM's `InstCount`
to generate a feature vector which comprises the total instruction
count, and the *density* of different kinds of instructions, e.g. The
number of floating point additions *per instruction*. This is
sufficient for my needs but it is worth noting that such a crude
metric of computation would likely fall down in the presence of
sufficient control flow, where in the static instruction counts would
no longer resemble the number of instructions *actually* executed.

Dataset features are simple by comparison. I merely record the width
and height of the input grid, and use C++ template functions to
stringify the input and output data types.

Once we have features, we can create a dataset from which will can
train machine learning classifiers. For each unique feature vector, we
provide a label which is the workgroup size which gave the best
performance under the given conditions.

An autotuner can now be inserted into SkelCL which performs runtime
feature extraction and classification before every stencil invocation.

![OmniTune system diagram](/images/2015-05-29-omnitune.png)

The implementation of this autotuner uses a three-tier client-server
model. A master server stores the labelled training data in a common
location. Then, for each SkelCL-capable machine, a system-level daemon
hosts a DBus session bus which SkelCL processes can communicate with
to request workgroup sizes. On launch, this daemon requests the latest
training data from the master server. When a SkelCL stencil is
invoked, it synchronously calls the `RequestParamValues()` method of
the autotuner daemon, passing as arguments the required data in order
to assemble a feature vector. Feature extraction then occurs within
the daemon, which classifies the datapoint and returns the suggested
workgroup size to the SkelCL process. This is a very low latency
operation, and the system daemon can handle multiple connections from
separate SkelCL processes simultaneously (although this is an
admittedly unlikely use-case given that most GPGPU programs expect to
be run in isolation).

![OmniTune communication diagram](/images/2015-05-29-comms.png)

So how well does the system perform? To evaluate this, the autotuner
was tested on unseen programs, and the speedup relative to the ZeroR
autotuner was recorded.

![Speedup of OmniTune for unseen programs](/images/2015-05-29-test-speedups.png)

So it works! Using synthetic benchmarks, runtime feature extraction,
and machine learning, I can improve the performance of unseen SkelCL
stencil codes by an average of 2.8x.

One as yet under-explored area of this project is the feedback loop
that exists when SkelCL programs are allowed to submit new datapoints
to the dataset after execution with a given workgroup size has
completed. While implemented, this feature is used only for collecting
offline training data, and is not used for a runtime exploration of
the optimisation space. More on that to come!
