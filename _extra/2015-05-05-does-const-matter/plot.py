#!/usr/bin/env python

import os
import re
import subprocess
import sys

import matplotlib.pyplot as plt

import srtime

def set_optimisation_flags(flag_string):
    command = ("sed -r -i 's/(^OPTIMISATION_LEVEL = ).*/\\1{val}/' Makefile"
               .format(val=flag_string))
    os.system(command)

def make_rt():
    os.system("make clean")
    os.system("make ./examples/example1")

def parse_csv(lines):
    tokens = []
    for i in range(len(lines)):
        line = lines[i].rstrip().split(", ")
        if i > 0:
            for i in range(1, len(line)):
                try:
                    line[i] = float(line[i])
                except ValueError as e:
                    print(e, line)
                    sys.exit(1)
        tokens.append(line)
    return tokens

def format_num(number):
    return "{0:.3f}".format(number)

def plot(labels, x_vals, y_vals, err_vals, title, path):
    width = 1
    bar_args = {
        'width': 1,
        'color': ['grey', 'g', 'b', 'r', 'y'],
        'ecolor': 'k'
    }

    if err_vals:
        bar_args['yerr'] = err_vals

    plt.bar(x_vals, y_vals, **bar_args)
    plt.axes().yaxis.grid(b=True, which='major', color="#aaaaaa", linestyle='-')
    plt.ylabel("Runtime (seconds)", weight="bold")
    plt.xlabel("Optimisation level", weight="bold")
    plt.title(title, weight="bold")
    plt.axhline(y=1, color='k')
    plt.xticks([x + .5 for x in x_vals], labels)
    plt.ylim(ymax=max(y_vals) * 1.1)
    plt.savefig(path)
    plt.close()

def flatten(l):
    return [item for sublist in l for item in sublist]

def main():
    # Load our data from CSVs.
    input = open("const.csv")
    lines = parse_csv(input.readlines())
    const = lines[1:]
    input.close()

    input = open("baseline.csv")
    lines = parse_csv(input.readlines())
    baseline = lines[1:]
    input.close()

    labels = [x[0] for x in const]
    x_vals = range(len(const))

    const_c1_vals = [x[2] for x in const]
    const_y_vals = [x[3] for x in const]
    const_err_vals = [x - y for x,y in zip(const_y_vals, const_c1_vals)]

    num_runs = [x[-1] for x in const]

    baseline_c1_vals = [x[2] for x in baseline]
    baseline_y_vals = [x[3] for x in baseline]
    baseline_err_vals = [x - y for x,y in zip(baseline_y_vals,
                                              baseline_c1_vals)]

    plot(labels, x_vals, const_y_vals, const_err_vals,
         "With const keyword", "const.png")
    plot(labels, x_vals, baseline_y_vals, baseline_err_vals,
         "Baseline", "baseline.png")

    speedups = [x / y for x,y in zip(baseline_y_vals, const_y_vals)]
    plot(labels, x_vals, speedups, [], "Speedup", "speedup.png")

    labels = flatten([[x, x + " const"] for x in labels])
    x_vals = range(2 * len(const))
    y_vals = flatten([[x, y] for x,y in zip(baseline_y_vals, const_y_vals)])
    err_vals = flatten([[x, y] for x,y in zip(baseline_err_vals,
                                              const_err_vals)])
    plot(labels, x_vals, y_vals, err_vals, "Both", "both.png")



if __name__ == "__main__":
    main()
