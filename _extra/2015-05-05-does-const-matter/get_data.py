#!/usr/bin/env python

import subprocess
import re
import os

import srtime

TIME_RE = re.compile(".*traces in ([0-9]+.[0-9]+) .*")

def get_runtime():
    output = subprocess.check_output("./examples/example1").decode()
    match = re.search(TIME_RE, output)
    if match:
        return float(match.group(1))

def set_optimisation_flags(flag_string):
    command = ("sed -r -i 's/(^OPTIMISATION_LEVEL = ).*/\\1{val}/' Makefile"
               .format(val=flag_string))
    os.system(command)

def make_rt():
    os.system("make clean")
    os.system("make ./examples/example1")

def csv(*args, **kwargs):
    print(", ".join([str(x) for x in args]), **kwargs)

def main():
    output = open("./results.csv", "w")
    os.chdir(os.path.expanduser("~/src/rt"))

    optimisation_levels = [
        "-O0",
        "-O1",
        "-O2",
        "-O3",
        "-Os"
    ]
    num_runs = 100

    csv("olevel", "min", "c0", "mean", "c1", "max", "n", file=output)
    for olevel in optimisation_levels:
        set_optimisation_flags(olevel)
        make_rt()
        runtimes = []
        for i in range(num_runs):
            runtime = get_runtime()
            runtimes.append(runtime)

        conf = srtime.stats.confinterval(runtimes)
        rmin = min(runtimes)
        c0 = conf[0]
        mean = srtime.stats.mean(runtimes)
        c1 = conf[1]
        rmax = max(runtimes)
        csv(olevel, rmin, c0, mean, c1, rmax, num_runs, file=output)

    output.close()

if __name__ == "__main__":
    main()
